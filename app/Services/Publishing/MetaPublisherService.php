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
        if (! in_array($project->status, ['ready', 'scheduled'], true)) {
            throw new RuntimeException('O conteúdo precisa estar aprovado antes da publicação.');
        }

        if (! in_array($project->channel, ['instagram', 'facebook'], true)) {
            throw new RuntimeException('Publicação direta disponível apenas para Instagram e Facebook.');
        }

        $assetUrl = $this->ensureImageAsset($project);
        $caption = trim(implode("\n\n", array_filter([
            (string) $project->caption,
            (string) $project->cta,
            (string) $project->hashtags,
        ])));

        $response = $this->engineRequest('/api/internal/marketing/publisher/meta/publish', [
            'project_id' => $this->projectId(),
            'client_id' => (int) $project->client_id,
            'channel' => (string) $project->channel,
            'asset_url' => $assetUrl,
            'caption' => $caption,
            'type' => 'image',
        ]);

        if (! $response->successful() || ! $response->json('ok', false)) {
            $status = (string) $response->json('status', '');
            $error = (string) $response->json('error', '');

            if ($response->status() === 409 || $status === 'PUBLISHER_NOT_CONNECTED') {
                throw new RuntimeException('Nenhuma conta Meta publicadora está conectada para este cliente.');
            }

            throw new RuntimeException('A Meta não confirmou a publicação'.($error !== '' ? ': '.$error : '.'));
        }

        $result = (array) $response->json();

        if (strtoupper((string) ($result['status'] ?? '')) !== 'PUBLISHED' || empty($result['external_id'])) {
            throw new RuntimeException('A publicação ainda não foi confirmada pela Meta.');
        }

        $project->forceFill([
            'status' => 'published',
            'published_at' => now(),
            'scheduled_at' => null,
        ])->save();

        Storage::disk('local')->makeDirectory('publisher/publications');
        Storage::disk('local')->put(
            'publisher/publications/project-'.$project->id.'.json',
            json_encode($result + [
                'project_id' => $project->id,
                'client_id' => $project->client_id,
                'published_at' => now()->toIso8601String(),
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $result;
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
            ->timeout((int) config('services.marketing_engine.timeout', 150))
            ->post($baseUrl.$path, $payload);
    }

    private function projectId(): string
    {
        return (string) config('services.marketing_engine.project_id', 'vitrine-ai-social-enterprise');
    }

    private function ensureImageAsset(ContentProject $project): string
    {
        $path = 'publisher/assets/project-'.$project->id.'.png';

        if (! Storage::disk('public')->exists($path)) {
            $url = trim((string) config('services.marketing_engine.url', ''));
            $token = trim((string) config('services.marketing_engine.token', ''));

            if ($url === '' || $token === '') {
                throw new RuntimeException('Motor de mídia indisponível para preparar a publicação.');
            }

            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout((int) config('services.marketing_engine.timeout', 150))
                ->post($url, [
                    'project_id' => $this->projectId(),
                    'brand' => (string) ($project->brand?->name ?: 'Vitrine Social Midia'),
                    'idea' => (string) $project->idea,
                    'objective' => (string) $project->objective,
                    'channel' => (string) $project->channel,
                    'format' => (string) $project->format,
                    'title' => (string) $project->title,
                    'caption' => (string) $project->caption,
                    'cta' => (string) $project->cta,
                ]);

            $binary = base64_decode((string) $response->json('image_base64', ''), true);

            if (! $response->successful() || ! $response->json('ok') || $binary === false || $binary === '') {
                throw new RuntimeException('Não foi possível preparar a imagem para publicação. HTTP '.$response->status().'.');
            }

            Storage::disk('public')->put($path, $binary);
        }

        return route('publisher.asset', ['project' => $project->id]);
    }
}
