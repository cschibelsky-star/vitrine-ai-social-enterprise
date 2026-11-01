<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ContentProject;
use App\Services\Publishing\DualLedgerPublicationReservation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class DualLedgerPublicationReservationTest extends TestCase
{
    use RefreshDatabase;

    private function addLegacySchema(): void
    {
        Schema::table('content_projects', function (Blueprint $table) {
            $table->json('publishing_asset')->nullable();
            $table->string('approved_fingerprint', 64)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
        });
        Schema::create('publication_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_project_id')->unique();
            $table->string('idempotency_key', 64)->unique();
            $table->string('status');
            $table->string('external_id')->nullable();
            $table->string('error_code')->nullable();
            $table->timestamps();
        });
    }

    private function project(): array
    {
        $client = Client::create(['name' => 'Reservation test only']);
        $project = ContentProject::create([
            'client_id' => $client->id,
            'idea' => 'Never publish this fixture',
            'status' => 'ready',
            'channel' => 'instagram',
        ]);
        $asset = [
            'path' => 'publisher/assets/test-approved.jpg',
            'mime' => 'image/jpeg',
            'sha256' => str_repeat('a', 64),
        ];
        $fingerprint = hash('sha256', json_encode([
            $client->id, 'instagram', null, $asset, null, null, null,
        ], JSON_THROW_ON_ERROR));
        DB::table('content_projects')->where('id', $project->id)->update([
            'publishing_asset' => json_encode($asset, JSON_THROW_ON_ERROR),
            'approved_fingerprint' => $fingerprint,
            'approved_at' => now(),
            'approved_by' => 1,
        ]);
        return [$client, $project];
    }

    public function test_reserves_both_ledgers_atomically_without_network_or_jobs(): void
    {
        $this->addLegacySchema();
        Http::fake();
        Queue::fake();
        [$client, $project] = $this->project();

        $reservation = app(DualLedgerPublicationReservation::class)
            ->reserve($project->id, $client->id, 'vitrine-ai-social-enterprise');

        $this->assertSame('reserved', $reservation['status']);
        $this->assertSame(64, strlen($reservation['operation_key']));
        $this->assertSame(64, strlen($reservation['transport_key']));
        $this->assertDatabaseHas('social_publication_operations', [
            'content_project_id' => $project->id,
            'status' => 'queued',
            'idempotency_key' => $reservation['operation_key'],
        ]);
        $this->assertDatabaseHas('publication_attempts', [
            'content_project_id' => $project->id,
            'status' => 'reserved',
            'idempotency_key' => $reservation['transport_key'],
        ]);
        $this->assertNull($project->fresh()->published_at);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_duplicate_is_rejected_and_does_not_create_a_second_operation(): void
    {
        $this->addLegacySchema();
        [$client, $project] = $this->project();
        $service = app(DualLedgerPublicationReservation::class);
        $service->reserve($project->id, $client->id, 'vitrine-ai-social-enterprise');
        try {
            $service->reserve($project->id, $client->id, 'vitrine-ai-social-enterprise');
            $this->fail('Repeated reservation must not succeed');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('já reservada', $exception->getMessage());
        }
        $this->assertDatabaseCount('publication_attempts', 1);
        $this->assertDatabaseCount('social_publication_operations', 1);
    }

    public function test_changed_caption_after_approval_is_rejected_without_writes(): void
    {
        $this->addLegacySchema();
        [$client, $project] = $this->project();
        DB::table('content_projects')->where('id', $project->id)
            ->update(['caption' => 'Alterada após aprovação']);

        try {
            app(DualLedgerPublicationReservation::class)
                ->reserve($project->id, $client->id, 'vitrine-ai-social-enterprise');
            $this->fail('Content mutation must invalidate approval.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Conteúdo alterado', $exception->getMessage());
        }
        $this->assertDatabaseCount('publication_attempts', 0);
        $this->assertDatabaseCount('social_publication_operations', 0);
    }

    public function test_missing_approved_media_is_rejected(): void
    {
        $this->addLegacySchema();
        [$client, $project] = $this->project();
        DB::table('content_projects')->where('id', $project->id)
            ->update(['publishing_asset' => null]);

        try {
            app(DualLedgerPublicationReservation::class)
                ->reserve($project->id, $client->id, 'vitrine-ai-social-enterprise');
            $this->fail('Missing approved asset cannot be reserved.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Mídia aprovada ausente', $exception->getMessage());
        }
        $this->assertDatabaseCount('publication_attempts', 0);
        $this->assertDatabaseCount('social_publication_operations', 0);
    }

    public function test_missing_legacy_schema_fails_without_operation(): void
    {
        $this->expectException(RuntimeException::class);
        try {
            app(DualLedgerPublicationReservation::class)
                ->reserve(1, 1, 'vitrine-ai-social-enterprise');
        } finally {
            $this->assertDatabaseCount('social_publication_operations', 0);
        }
    }

    public function test_cross_tenant_reservation_is_rejected_without_writes(): void
    {
        $this->addLegacySchema();
        [$client, $project] = $this->project();
        $other = Client::create(['name' => 'Wrong tenant']);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        try {
            app(DualLedgerPublicationReservation::class)
                ->reserve($project->id, $other->id, 'vitrine-ai-social-enterprise');
        } finally {
            $this->assertDatabaseCount('publication_attempts', 0);
            $this->assertDatabaseCount('social_publication_operations', 0);
        }
    }
}
