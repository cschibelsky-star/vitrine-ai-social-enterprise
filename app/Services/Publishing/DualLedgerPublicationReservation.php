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

            // Recompute exactly the approval fingerprint contract of PR #9.
            // This validates approved metadata, not the stored binary; file
            // integrity and payload/capability checks remain separate gates.
            $asset = json_decode((string) $project->getRawOriginal('publishing_asset'), true);
            if (! is_array($asset)
                || empty($asset['path'])
                || ! preg_match('/^[a-f0-9]{64}$/', (string) ($asset['sha256'] ?? ''))
                || ! in_array($asset['mime'] ?? '', ['image/jpeg', 'image/png', 'video/mp4'], true)) {
                throw new RuntimeException('Mídia aprovada ausente ou inválida.');
            }
            // Never reserve an unavailable or modified final file.
            // Use the same local asset disk as ApprovedAssetService in PR #9.
            $disk = \Illuminate\Support\Facades\Storage::disk('local');
            if (! $disk->exists($asset['path'])
                || ! hash_equals((string) $asset['sha256'], hash_file('sha256', $disk->path($asset['path'])))) {
                throw new RuntimeException('Arquivo final aprovado ausente ou alterado.');
            }
            $expectedFingerprint = hash('sha256', json_encode([
                $project->client_id,
                $project->channel,
                $project->format,
                $asset,
                $project->caption,
                $project->cta,
                $project->hashtags,
            ], JSON_THROW_ON_ERROR));
            if (! hash_equals($expectedFingerprint, (string) $project->approved_fingerprint)) {
                throw new RuntimeException('Conteúdo alterado após aprovação; nova revisão obrigatória.');
            }
            if ($project->status === 'scheduled'
                && (! $project->scheduled_at || $project->scheduled_at->isFuture())) {
                throw new RuntimeException('Agendamento ainda não venceu.');
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
