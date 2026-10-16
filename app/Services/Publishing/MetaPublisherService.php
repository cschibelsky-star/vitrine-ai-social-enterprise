<?php

namespace App\Services\Publishing;

use App\Models\ContentProject;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class MetaPublisherService
{
    public function connectionStatus(int $clientId): array
    {
        try {
            $response = $this->engineRequest('/api/internal/marketing/publisher/meta/status', [
                'project_id' => $this->projectId(),
                'client_id' => $clientId,
            ]);

            return [
                'connected' => (bool) $response->json('connected', false),
                'page_name' => $response->json('page_name'),
                'instagram_username' => $response->json('instagram_username'),
                'connected_at' => $response->json('connected_at'),
            ];
        } catch (Throwable $exception) {
            report($exception);

            return [
                'connected' => false,
                'page_name' => null,
                'instagram_username' => null,
                'connected_at' => null,
            ];
        }
    }

    public function beginConnection(int $clientId, string $returnUrl): string
    {
        $response = $this->engineRequest('/api/internal/marketing/publisher/meta/connect-url', [
            'project_id' => $this->projectId(),
            'client_id' => $clientId,
            'return_url' => $returnUrl,
        ]);

        $url = trim((string) $response->json('authorization_url', ''));

        if (! $response->successful() || ! $response->json('ok') || $url === '') {
            throw new RuntimeException('Não foi possível iniciar a autorização Meta.');
        }

        return $url;
    }

    public function disconnect(int $clientId): void
    {
        $response = $this->engineRequest('/api/internal/marketing/publisher/meta/disconnect', [
            'project_id' => $this->projectId(),
            'client_id' => $clientId,
        ]);

        if (! $response->successful() || ! $response->json('ok')) {
            throw new RuntimeException('Não foi possível desconectar a conta Meta.');
        }
    }

    public function publish(ContentProject $project): array
    {
        if (! config('publishing.enabled', false)) {
            throw new RuntimeException('Publicação externa desativada. Homologação não publica nas redes.');
        }
        $contract = new PublicationContract;
        $project->refresh();
        $existing = \Illuminate\Support\Facades\DB::table('publication_attempts')->where('content_project_id', $project->id)->first();
        if ($existing) {
            if ($existing->status === 'published') {
                return ['ok' => true, 'status' => 'PUBLISHED', 'external_id' => $existing->external_id];
            }
            throw new RuntimeException('Envio já registrado; reconciliar a tentativa antes de reenviar.');
        }
        if (! in_array($project->status, ['ready', 'scheduled'], true) || ! $project->approved_at
            || ! hash_equals($project->approved_fingerprint ?? '', $contract->fingerprint($project))) {
            throw new RuntimeException('Revise e aprove o arquivo final antes da publicação.');
        }
        $snapshot = $project->approved_fingerprint;
        $payload = $contract->payload($project);
        (new ApprovedAssetService)->verify($project);
        // Capability discovery must guarantee durable idempotency on the receiver.
        $status = $this->engineRequest('/api/internal/marketing/publisher/meta/status', [
            'project_id' => $this->projectId(), 'client_id' => (int) $project->client_id,
        ]);
        $capability = $project->channel.'.'.$payload['placement'].'.'.$payload['type'];
        if (! $status->successful() || ! $status->json('connected', false)
            || (int) $status->json('contract_version', 0) !== 2
            || $status->json('durable_idempotency') !== true
            || ! in_array($capability, (array) $status->json('capabilities', []), true)) {
            throw new RuntimeException('Publicador não autorizado ou contrato v2/idempotência/formato indisponível.');
        }
        $asset = $project->publishing_asset;
        $assetUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('publisher.approved-asset', now()->addHours(24), [
            'project' => $project->id, 'hash' => $asset['sha256'],
        ]);
        if (parse_url($assetUrl, PHP_URL_SCHEME) !== 'https') {
            throw new RuntimeException('A mídia exige URL pública HTTPS.');
        }
        $key = \Illuminate\Support\Facades\DB::transaction(function () use ($project, $contract, $snapshot) {
            $fresh = ContentProject::query()->lockForUpdate()->findOrFail($project->id);
            if (! in_array($fresh->status, ['ready', 'scheduled'], true) || $fresh->published_at
                || ! $fresh->approved_at || ! $fresh->approved_by
                || ! hash_equals($snapshot, $fresh->approved_fingerprint ?? '')
                || ! hash_equals($fresh->approved_fingerprint ?? '', $contract->fingerprint($fresh))) {
                throw new RuntimeException('Aprovação ausente ou conteúdo alterado; revise e aprove novamente.');
            }
            if ($fresh->status === 'scheduled' && (! $fresh->scheduled_at || $fresh->scheduled_at->isFuture())) {
                throw new RuntimeException('Agendamento ainda não venceu.');
            }
            if (\Illuminate\Support\Facades\DB::table('publication_attempts')->where('content_project_id', $fresh->id)->exists()) {
                throw new RuntimeException('Tentativa já registrada.');
            }
            $key = hash('sha256', $this->projectId().'|'.$fresh->id.'|'.$fresh->approved_fingerprint);
            \Illuminate\Support\Facades\DB::table('publication_attempts')->insert([
                'content_project_id' => $fresh->id, 'idempotency_key' => $key, 'status' => 'submitted',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $fresh->forceFill(['status' => 'publishing'])->save();
            return $key;
        });
        try {
            $response = $this->engineRequest('/api/internal/marketing/publisher/meta/publish', $payload + [
                'project_id' => $this->projectId(), 'asset_url' => $assetUrl, 'idempotency_key' => $key,
            ]);
            if ($response->successful() && $response->json('ok') === true
                && strtoupper((string) $response->json('status')) === 'PUBLISHED'
                && is_string($response->json('external_id')) && $response->json('external_id') !== '') {
                $externalId = $response->json('external_id');
                \Illuminate\Support\Facades\DB::transaction(function () use ($project, $externalId) {
                    \Illuminate\Support\Facades\DB::table('publication_attempts')->where('content_project_id', $project->id)->update([
                        'status' => 'published', 'external_id' => $externalId, 'updated_at' => now(),
                    ]);
                    ContentProject::query()->whereKey($project->id)->update([
                        'status' => 'published', 'published_at' => now(), 'scheduled_at' => null,
                    ]);
                });
                return ['ok' => true, 'status' => 'PUBLISHED', 'external_id' => $externalId];
            }
            $state = $response->successful() && $response->json('ok') === true
                && in_array(strtoupper((string) $response->json('status')), ['PROCESSING', 'PENDING'], true)
                ? 'processing' : 'unknown';
            $this->recordUnconfirmed($project->id, $state, 'META_UNCONFIRMED');
            throw new RuntimeException('Envio sem confirmação final. Aguarde reconciliação; não reenvie.');
        } catch (Throwable $exception) {
            \Illuminate\Support\Facades\DB::table('publication_attempts')
                ->where('content_project_id', $project->id)->where('status', 'submitted')
                ->update(['status' => 'unknown', 'error_code' => 'TRANSPORT_OR_PERSISTENCE_FAILURE', 'updated_at' => now()]);
            ContentProject::query()->whereKey($project->id)->where('status', 'publishing')->update(['status' => 'publication_unknown']);
            // Do not expose provider response, signed URLs or tokens in UI/logs.
            throw new RuntimeException('Resultado pendente de reconciliação; reenvio bloqueado.');
        }
    }

    public function reconcile(ContentProject $project): array
    {
        if (! config('publishing.enabled', false)) { throw new RuntimeException('Publicação externa desativada.'); }
        $attempt = \Illuminate\Support\Facades\DB::table('publication_attempts')->where('content_project_id', $project->id)->first();
        if (! $attempt) { throw new RuntimeException('Nenhuma tentativa registrada.'); }
        if ($attempt->status === 'published') { return ['status' => 'PUBLISHED', 'external_id' => $attempt->external_id]; }
        // This endpoint may only read the receiver's ledger; never initiate/resubmit publication.
        $response = $this->engineRequest('/api/internal/marketing/publisher/meta/publication-status', [
            'project_id' => $this->projectId(), 'client_id' => (int) $project->client_id,
            'content_project_id' => (int) $project->id, 'idempotency_key' => $attempt->idempotency_key,
        ]);
        if (! $response->successful() || $response->json('ok') !== true
            || ! hash_equals($attempt->idempotency_key, (string) $response->json('idempotency_key', ''))) {
            throw new RuntimeException('Reconciliação não confirmada; nenhum reenvio realizado.');
        }
        if ($response->json('status') === 'PUBLISHED' && is_string($response->json('external_id')) && $response->json('external_id') !== '') {
            \Illuminate\Support\Facades\DB::transaction(function () use ($project, $response) {
                \Illuminate\Support\Facades\DB::table('publication_attempts')->where('content_project_id', $project->id)->update([
                    'status' => 'published', 'external_id' => $response->json('external_id'), 'error_code' => null, 'updated_at' => now(),
                ]);
                ContentProject::query()->whereKey($project->id)->update(['status' => 'published', 'published_at' => now(), 'scheduled_at' => null]);
            });
        }
        return ['status' => $response->json('status'), 'external_id' => $response->json('external_id')];
    }

    private function recordUnconfirmed(int $projectId, string $state, string $code): void
    {
        \Illuminate\Support\Facades\DB::table('publication_attempts')->where('content_project_id', $projectId)->update([
            'status' => $state, 'error_code' => $code, 'updated_at' => now(),
        ]);
        ContentProject::query()->whereKey($projectId)->update(['status' => 'publication_'.$state]);
    }

    private function engineRequest(string $path, array $payload)
    {
        $token = trim((string) config('services.marketing_engine.token', ''));
        $baseUrl = rtrim((string) config('services.marketing_engine.base_url', 'http://vitrine_marketing_web_internal_hml'), '/');

        if ($token === '' || $baseUrl === '') {
            throw new RuntimeException('Motor de publicação indisponível.');
        }

        return Http::withToken($token)
            ->acceptJson()
            ->connectTimeout(5)
            ->timeout(30)
            ->post($baseUrl.$path, $payload);
    }

    private function projectId(): string
    {
        return (string) config('services.marketing_engine.project_id', 'vitrine-ai-social-enterprise');
    }

}
