<?php

namespace App\Services\Publishing;

use App\Models\ContentProject;
use App\Models\SocialPublicationOperation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Staged, persistence-only compatibility reservation for PR #9 + PR #12.
 * Does not publish, dispatch, call Meta or enable either existing publisher.
 * Future integration must explicitly reconcile and activate a single owner.
 */
final class DualLedgerPublicationReservation
{
    public function reserve(int $projectId, int $clientId, string $marketingProjectId): array
    {
        // Only run in a schema with BOTH fully migrated ledgers.
        if (! Schema::hasTable('publication_attempts')
            || ! Schema::hasTable('social_publication_operations')) {
            throw new RuntimeException('Ambos os ledgers precisam existir antes da reserva.');
        }

        return DB::transaction(function () use ($projectId, $clientId, $marketingProjectId) {
            $project = ContentProject::query()
                ->whereKey($projectId)
                ->where('client_id', $clientId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($project->status, ['ready', 'scheduled'], true)
                || $project->published_at !== null
                || empty($project->approved_fingerprint)
                || empty($project->approved_at)
                || empty($project->approved_by)
                || ! in_array($project->channel, ['facebook', 'instagram'], true)) {
                throw new RuntimeException('Reserva exige publicação aprovada e íntegra.');
            }

            // A single attempt per content project is the strict PR #9 contract.
            if (DB::table('publication_attempts')
                ->where('content_project_id', $projectId)->exists()) {
                throw new RuntimeException('Peça já reservada ou enviada; reconciliação obrigatória.');
            }

            $version = (string) $project->approved_fingerprint;
            $operationKey = SocialPublicationOperation::idempotencyKey(
                $clientId, $projectId, (string) $project->channel, $version
            );
            // Preserve the transport key PR #9 already sends to Marketing PR #102.
            $transportKey = hash('sha256', $marketingProjectId.'|'.$projectId.'|'.$version);

            $operationId = DB::table('social_publication_operations')->insertGetId([
                'client_id' => $clientId,
                'content_project_id' => $projectId,
                'channel' => $project->channel,
                'content_version' => $version,
                'idempotency_key' => $operationKey,
                'status' => SocialPublicationOperation::QUEUED,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('publication_attempts')->insert([
                'content_project_id' => $projectId,
                'idempotency_key' => $transportKey,
                // Reserved is not submitted: no external request has been made.
                'status' => 'reserved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'operation_id' => $operationId,
                'operation_key' => $operationKey,
                'transport_key' => $transportKey,
                'status' => 'reserved',
            ];
        });
    }
}
