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

        if ($attempt->status === 'published'
            && $operation->status === SocialPublicationOperation::PUBLISHED
            && ! empty($attempt->external_id)
            && hash_equals((string) $attempt->external_id, (string) $operation->external_id)
            && $project->published_at !== null) {
            return ['status' => 'PUBLISHED', 'reason' => 'BOTH_CONFIRMED'];
        }

        if ($attempt->status === 'reserved'
            && $operation->status === SocialPublicationOperation::QUEUED
            && (int) $operation->attempts === 0
            && empty($attempt->external_id)
            && empty($operation->external_id)
            && empty($operation->provider_container_id)
            && $project->published_at === null) {
            // Diagnostic only: not an authorization to dispatch.
            return ['status' => 'RESERVED', 'reason' => 'NO_SUBMISSION_RECORDED'];
        }

        // Any partial confirmation or uncertain result must be reconciled
        // by an operator against the Marketing receiver/provider before retry.
        return ['status' => 'BLOCKED', 'reason' => 'RECONCILIATION_REQUIRED'];
    }
}
