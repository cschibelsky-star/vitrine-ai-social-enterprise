<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ContentProject;
use App\Models\SocialPublicationOperation;
use App\Services\Publishing\PublicationOperationClaim;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SocialPublicationOperationPersistenceTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $client = Client::create(['name' => 'HML isolated client']);
        $project = ContentProject::create([
            'client_id' => $client->id,
            'idea' => 'Test fixture only; never publish',
        ]);
        return [$client, $project];
    }

    private function operation(int $clientId, int $projectId, string $channel = 'instagram', string $version = 'v1'): array
    {
        return [
            'client_id' => $clientId,
            'content_project_id' => $projectId,
            'channel' => $channel,
            'content_version' => $version,
            'idempotency_key' => SocialPublicationOperation::idempotencyKey($clientId, $projectId, $channel, $version),
            'status' => SocialPublicationOperation::QUEUED,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function test_migration_builds_unique_indexes_without_publishing(): void
    {
        [$client, $project] = $this->fixture();
        DB::table('social_publication_operations')->insert($this->operation($client->id, $project->id));
        $this->assertDatabaseCount('social_publication_operations', 1);
        $this->assertDatabaseHas('content_projects', ['id' => $project->id, 'published_at' => null]);
        $this->assertSame('queued', DB::table('social_publication_operations')->value('status'));
    }

    public function test_duplicate_operation_is_rejected_by_database_identity_constraint(): void
    {
        [$client, $project] = $this->fixture();
        $first = $this->operation($client->id, $project->id);
        DB::table('social_publication_operations')->insert($first);
        $duplicate = $first;
        $duplicate['idempotency_key'] = str_repeat('a', 64); // Distinct key must not bypass identity constraint.
        try {
            DB::table('social_publication_operations')->insert($duplicate);
            $this->fail('Duplicate logical publication was accepted');
        } catch (QueryException $expected) {
            $this->assertDatabaseCount('social_publication_operations', 1);
        }
    }

    public function test_duplicate_idempotency_key_is_rejected_even_with_distinct_identity(): void
    {
        [$client, $project] = $this->fixture();
        $first = $this->operation($client->id, $project->id);
        DB::table('social_publication_operations')->insert($first);
        $duplicate = $this->operation($client->id, $project->id, 'facebook');
        $duplicate['idempotency_key'] = $first['idempotency_key'];
        try {
            DB::table('social_publication_operations')->insert($duplicate);
            $this->fail('Duplicate idempotency key was accepted');
        } catch (QueryException $expected) {
            $this->assertDatabaseCount('social_publication_operations', 1);
        }
    }

    public function test_versions_channels_and_clients_have_distinct_operations(): void
    {
        [$client, $project] = $this->fixture();
        [$otherClient, $otherProject] = $this->fixture();
        foreach ([
            $this->operation($client->id, $project->id, 'instagram', 'v1'),
            $this->operation($client->id, $project->id, 'instagram', 'v2'),
            $this->operation($client->id, $project->id, 'facebook', 'v1'),
            $this->operation($otherClient->id, $otherProject->id, 'instagram', 'v1'),
        ] as $operation) {
            DB::table('social_publication_operations')->insert($operation);
        }
        $this->assertDatabaseCount('social_publication_operations', 4);
        $this->assertSame(3, DB::table('social_publication_operations')->where('client_id', $client->id)->count());
        $this->assertSame(1, DB::table('social_publication_operations')->where('client_id', $otherClient->id)->count());
    }

    public function test_tenant_cannot_reference_another_clients_project(): void
    {
        [$client, $project] = $this->fixture();
        [$otherClient, $otherProject] = $this->fixture();
        try {
            DB::table('social_publication_operations')->insert($this->operation($client->id, $otherProject->id));
            $this->fail('Cross-tenant project reference was accepted');
        } catch (QueryException $expected) {
            $this->assertDatabaseCount('social_publication_operations', 0);
        }
    }

    public function test_orphan_project_cannot_be_used_for_publication(): void
    {
        [$client, $project] = $this->fixture();
        $project->update(['client_id' => null]);
        try {
            DB::table('social_publication_operations')->insert($this->operation($client->id, $project->id));
            $this->fail('Unowned project was accepted');
        } catch (QueryException $expected) {
            $this->assertDatabaseCount('social_publication_operations', 0);
        }
    }

    public function test_only_one_claim_succeeds_and_repeated_claims_are_blocked(): void
    {
        [$client, $project] = $this->fixture();
        $id = DB::table('social_publication_operations')->insertGetId($this->operation($client->id, $project->id));
        $gate = new PublicationOperationClaim();
        $this->assertTrue($gate->claim($id, $client->id));
        $this->assertFalse($gate->claim($id, $client->id));
        $this->assertFalse($gate->claim($id, $client->id + 1000));
        $this->assertDatabaseHas('social_publication_operations', [
            'id' => $id, 'status' => SocialPublicationOperation::PUBLISHING, 'attempts' => 1,
        ]);
    }

    public function test_uncertain_result_never_reclaims_operation(): void
    {
        [$client, $project] = $this->fixture();
        $id = DB::table('social_publication_operations')->insertGetId($this->operation($client->id, $project->id));
        $gate = new PublicationOperationClaim();
        $this->assertTrue($gate->claim($id, $client->id));
        $this->assertTrue($gate->markUncertain($id, $client->id));
        $this->assertFalse($gate->claim($id, $client->id));
        $this->assertFalse($gate->markUncertain($id, $client->id));
        $this->assertDatabaseHas('social_publication_operations', [
            'id' => $id, 'status' => SocialPublicationOperation::PENDING_CONFIRMATION, 'attempts' => 1,
        ]);
        $this->assertDatabaseHas('content_projects', ['id' => $project->id, 'published_at' => null]);
    }

    public function test_pending_confirmation_state_does_not_schedule_any_job_by_itself(): void
    {
        [$client, $project] = $this->fixture();
        $operation = $this->operation($client->id, $project->id);
        $operation['status'] = SocialPublicationOperation::PENDING_CONFIRMATION;
        DB::table('social_publication_operations')->insert($operation);
        $this->assertDatabaseHas('social_publication_operations', [
            'client_id' => $client->id,
            'content_project_id' => $project->id,
            'status' => SocialPublicationOperation::PENDING_CONFIRMATION,
            'attempts' => 0,
        ]);
        $this->assertDatabaseHas('content_projects', ['id' => $project->id, 'published_at' => null]);
    }
}
