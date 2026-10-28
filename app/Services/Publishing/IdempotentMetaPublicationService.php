<?php

namespace App\Services\Publishing;

use App\Models\ContentProject;
use App\Models\SocialPublicationOperation;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Opt-in publication boundary. Not wired to scheduled jobs until HML approval.
 * For an unknown Meta outcome the operation is held for reconciliation; never auto-resend.
 */
final class IdempotentMetaPublicationService
{
    public function __construct(
        private readonly MetaPublisherService $publisher,
        private readonly PublicationOperationClaim $gate
    ) {}

    public function publish(ContentProject $project): array
    {
        if (! $project->exists || ! $project->client_id) {
            throw new RuntimeException('O projeto precisa pertencer a um cliente persistido.');
        }
        if (! in_array($project->status, ['ready', 'scheduled'], true)) {
            throw new RuntimeException('Somente conteúdo aprovado pode ser publicado.');
        }
        if (! in_array($project->channel, ['instagram', 'facebook'], true)) {
            throw new RuntimeException('Canal não suportado.');
        }

        $version = hash('sha256', json_encode([
            $project->idea, $project->caption, $project->cta, $project->hashtags,
            $project->format, $project->channel,
        ], JSON_THROW_ON_ERROR));

        $identity = [
            'client_id' => (int) $project->client_id,
            'content_project_id' => (int) $project->id,
            'channel' => (string) $project->channel,
            'content_version' => $version,
        ];
        $key = SocialPublicationOperation::idempotencyKey(
            $identity['client_id'],
            $identity['content_project_id'],
            $identity['channel'],
            $identity['content_version']
        );

        $operation = DB::transaction(function () use ($identity, $key) {
            SocialPublicationOperation::query()->insertOrIgnore([
                ...$identity, 'idempotency_key' => $key,
                'status' => SocialPublicationOperation::QUEUED,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            return SocialPublicationOperation::query()
                ->where('idempotency_key', $key)->firstOrFail();
        });

        if (! $this->gate->claim($operation->id, $identity['client_id'])) {
            return [
                'ok' => false, 'status' => 'ALREADY_HANDLED_OR_PENDING',
                'operation_id' => $operation->id,
                'operation_status' => $operation->fresh()->status,
            ];
        }

        try {
            $result = $this->publisher->publish($project);
            if (strtoupper((string) ($result['status'] ?? '')) !== 'PUBLISHED'
                || empty($result['external_id'])) {
                $this->gate->markUncertain($operation->id, $identity['client_id']);
                return [
                    'ok' => false, 'status' => 'PENDING_CONFIRMATION',
                    'operation_id' => $operation->id,
                ];
            }

            $operation->forceFill([
                'status' => SocialPublicationOperation::PUBLISHED,
                'external_id' => (string) $result['external_id'],
                'provider_container_id' => $result['container_id'] ?? null,
                'confirmed_at' => now(),
            ])->save();

            return $result + ['operation_id' => $operation->id];
        } catch (Throwable $exception) {
            // A timeout/connection failure might happen AFTER Meta accepted the post.
            // The safest default is to block resend until operator reconciliation.
            $this->gate->markUncertain($operation->id, $identity['client_id']);
            report($exception);
            return [
                'ok' => false, 'status' => 'PENDING_CONFIRMATION',
                'operation_id' => $operation->id,
            ];
        }
    }
}
