<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ContentProject;
use App\Services\Publishing\IdempotentMetaPublicationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class PublishingLedgerCoexistenceGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_attempts_table_blocks_second_publisher_without_any_side_effect(): void
    {
        Http::fake();
        Queue::fake();

        // Simulates the PR #9 migration being present alongside PR #12.
        // The guard must reject before creating an operation or sending HTTP.
        Schema::create('publication_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_project_id');
            $table->string('idempotency_key', 64);
        });

        $client = Client::create(['name' => 'Isolated guard test']);
        $project = ContentProject::create([
            'client_id' => $client->id,
            'idea' => 'Never publish in test',
            'status' => 'ready',
            'channel' => 'instagram',
        ]);

        try {
            app(IdempotentMetaPublicationService::class)->publish($project);
            $this->fail('Competing ledgers must fail closed.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('integração dos ledgers pendente', $exception->getMessage());
        }

        $this->assertDatabaseCount('social_publication_operations', 0);
        $this->assertDatabaseCount('publication_attempts', 0);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }
}
