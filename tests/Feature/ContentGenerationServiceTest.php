<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\ContentProject;
use App\Services\AI\AiContentService;
use App\Services\ContentGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class ContentGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generation_consumes_one_content_credit(): void
    {
        $client = Client::query()->create([
            'name' => 'Cliente Geração',
            'status' => 'active',
        ]);

        ClientBalance::query()->create([
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'granted' => 2,
            'consumed' => 0,
            'available' => 2,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        $project = ContentProject::query()->create([
            'client_id' => $client->id,
            'title' => 'Conteúdo teste',
            'idea' => 'Oferta especial',
            'content_type' => 'post',
            'generation_method' => 'from_scratch',
            'objective' => 'sales',
            'format' => 'post_portrait',
            'channel' => 'instagram',
            'status' => 'draft',
        ]);

        $this->mock(AiContentService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('generateProject')
                ->once()
                ->andReturn(['ok' => true]);
        });

        $result = app(ContentGenerationService::class)->generate($project);

        $this->assertSame(['ok' => true], $result);

        $balance = ClientBalance::query()->where('client_id', $client->id)->firstOrFail();
        $this->assertSame('1.00', $balance->available);
        $this->assertSame('1.00', $balance->consumed);

        $this->assertDatabaseHas('consumption_ledgers', [
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'movement_type' => 'consumption',
            'amount' => 1,
            'unit' => 'content',
            'reference_type' => ContentProject::class,
            'reference_id' => $project->id,
        ]);
    }

    public function test_generation_failure_does_not_consume_credit(): void
    {
        $client = Client::query()->create([
            'name' => 'Cliente Falha',
            'status' => 'active',
        ]);

        ClientBalance::query()->create([
            'client_id' => $client->id,
            'balance_type' => 'content_credit',
            'granted' => 2,
            'consumed' => 0,
            'available' => 2,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        $project = ContentProject::query()->create([
            'client_id' => $client->id,
            'title' => 'Conteúdo falha',
            'idea' => 'Tema de teste',
            'content_type' => 'post',
            'generation_method' => 'from_scratch',
            'objective' => 'sales',
            'format' => 'post_portrait',
            'channel' => 'instagram',
            'status' => 'draft',
        ]);

        $this->mock(AiContentService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('generateProject')
                ->once()
                ->andThrow(new RuntimeException('Falha simulada da IA.'));
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Falha simulada da IA.');

        try {
            app(ContentGenerationService::class)->generate($project);
        } finally {
            $balance = ClientBalance::query()->where('client_id', $client->id)->firstOrFail();
            $this->assertSame('2.00', $balance->available);
            $this->assertSame('0.00', $balance->consumed);
            $this->assertDatabaseCount('consumption_ledgers', 0);
        }
    }
}
