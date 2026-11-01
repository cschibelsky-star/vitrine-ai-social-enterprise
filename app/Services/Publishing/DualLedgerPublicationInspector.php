<?php

namespace App\Services\Publishing;

use App\Models\ContentProject;
use App\Models\SocialPublicationOperation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Read-only, conservative reconciliation of the two local Social ledgers.
 * Never attempts provider status lookup, publication, retry or state mutation.
 */
final class DualLedgerPublicationInspector
{
    public function inspect(int $projectId, int $clientId, string $marketingProjectId): array
    {
        if (! Schema::hasTable('publication_attempts')
            || ! Schema::hasTable('social_publication_operations')) {
            return ['status' => 'BLOCKED', 'reason' => 'SCHEMA_INCOMPLETE'];
        }

        $project = ContentProject::query()
            ->whereKey($projectId)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $attempt = DB::table('publication_attempts')
            ->where('content_project_id', $project->id)->first();
        $operations = DB::table('social_publication_operations')
            ->where('content_project_id', $project->id)
            ->where('client_id', $clientId)
            ->get();

        if (! $attempt && $operations->isEmpty()) {
            return ['status' => 'BLOCKED', 'reason' => 'NO_RESERVATION'];
        }

        if (! $attempt || $operations->count() !== 1) {
            return ['status' => 'BLOCKED', 'reason' => 'LEDGER_MISMATCH'];
        }

        $operation = $operations->first();
        $version = (string) $operation->content_version;
        $expectedOperationKey = SocialPublicationOperation::idempotencyKey(
            $clientId, $projectId, (string) $operation->channel, $version
        );
        $expectedTransportKey = hash('sha256', $marketingProjectId.'|'.$projectId.'|'.$version);

        if ($version === ''
            || ! hash_equals($expectedOperationKey, (string) $operation->idempotency_key)
            || ! hash_equals($expectedTransportKey, (string) $attempt->idempotency_key)
            || (string) $operation->channel !== (string) $project->channel
            || ! hash_equals($version, (string) $project->approved_fingerprint)) {
            return ['status' => 'BLOCKED', 'reason' => 'IDENTITY_MISMATCH'];
        }

        return (new DualLedgerPublicationStatePolicy)->inspect(
            (string) $attempt->status,
            (string) $operation->status,
            (int) $operation->attempts,
            $attempt->external_id,
            $operation->external_id,
            $operation->provider_container_id,
            $project->published_at !== null
        );
    }
}
