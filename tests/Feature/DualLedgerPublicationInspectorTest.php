<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ContentProject;
use App\Models\SocialPublicationOperation;
use App\Services\Publishing\DualLedgerPublicationInspector;
use App\Services\Publishing\DualLedgerPublicationReservation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DualLedgerPublicationInspectorTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        Storage::fake('local');
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
        $client = Client::create(['name' => 'Inspector isolated test']);
        $project = ContentProject::create([
            'client_id' => $client->id, 'idea' => 'Never publish',
            'channel' => 'instagram', 'status' => 'ready',
        ]);
        $bytes = 'approved-media-byte-string';
        $path = 'publisher/assets/inspection.jpg';
        Storage::disk('local')->put($path, $bytes);
        $asset = ['path' => $path, 'mime' => 'image/jpeg', 'sha256' => hash('sha256', $bytes)];
        DB::table('content_projects')->where('id', $project->id)->update([
            'publishing_asset' => json_encode($asset, JSON_THROW_ON_ERROR),
            'approved_at' => now(), 'approved_by' => 1,
        ]);
        $fresh = $project->fresh();
        $fingerprint = hash('sha256', json_encode([
            $fresh->client_id, $fresh->channel, $fresh->format,
            $asset, $fresh->caption, $fresh->cta, $fresh->hashtags,
        ], JSON_THROW_ON_ERROR));
        DB::table('content_projects')->where('id', $project->id)->update([
            'approved_fingerprint' => $fingerprint,
        ]);
        return [$client, $project];
    }

    public function test_reserved_pair_is_detected_without_side_effects(): void
    {
        Http::fake();
        Queue::fake();
        [$client, $project] = $this->fixture();
        app(DualLedgerPublicationReservation::class)->reserve(
            $project->id, $client->id, 'vitrine-ai-social-enterprise'
        );
        $result = app(DualLedgerPublicationInspector::class)->inspect(
            $project->id, $client->id, 'vitrine-ai-social-enterprise'
        );
        $this->assertSame(['status' => 'RESERVED', 'reason' => 'NO_SUBMISSION_RECORDED'], $result);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_partial_confirmation_is_blocked_without_retry(): void
    {
        Http::fake();
        Queue::fake();
        [$client, $project] = $this->fixture();
        $reservation = app(DualLedgerPublicationReservation::class)->reserve(
            $project->id, $client->id, 'vitrine-ai-social-enterprise'
        );
        DB::table('publication_attempts')->where('content_project_id', $project->id)->update([
            'status' => 'unknown',
        ]);
        $result = app(DualLedgerPublicationInspector::class)->inspect(
            $project->id, $client->id, 'vitrine-ai-social-enterprise'
        );
        $this->assertSame(['status' => 'BLOCKED', 'reason' => 'RECONCILIATION_REQUIRED'], $result);
        $this->assertDatabaseHas('social_publication_operations', [
            'id' => $reservation['operation_id'], 'status' => 'queued',
        ]);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_identity_mismatch_is_blocked(): void
    {
        [$client, $project] = $this->fixture();
        app(DualLedgerPublicationReservation::class)->reserve(
            $project->id, $client->id, 'vitrine-ai-social-enterprise'
        );
        $result = app(DualLedgerPublicationInspector::class)->inspect(
            $project->id, $client->id, 'wrong-marketing-project'
        );
        $this->assertSame(['status' => 'BLOCKED', 'reason' => 'IDENTITY_MISMATCH'], $result);
    }

    public function test_matching_confirmed_pair_is_read_only(): void
    {
        Http::fake();
        Queue::fake();
        [$client, $project] = $this->fixture();
        $reservation = app(DualLedgerPublicationReservation::class)->reserve(
            $project->id, $client->id, 'vitrine-ai-social-enterprise'
        );
        DB::table('publication_attempts')->where('content_project_id', $project->id)->update([
            'status' => 'published', 'external_id' => 'meta-123',
        ]);
        DB::table('social_publication_operations')->where('id', $reservation['operation_id'])->update([
            'status' => SocialPublicationOperation::PUBLISHED, 'external_id' => 'meta-123',
        ]);
        DB::table('content_projects')->where('id', $project->id)->update([
            'published_at' => now(),
        ]);
        $result = app(DualLedgerPublicationInspector::class)->inspect(
            $project->id, $client->id, 'vitrine-ai-social-enterprise'
        );
        $this->assertSame(['status' => 'PUBLISHED', 'reason' => 'BOTH_CONFIRMED'], $result);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }
}
