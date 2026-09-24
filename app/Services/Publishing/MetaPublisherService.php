<?php

namespace App\Services\Publishing;

use App\Models\ContentProject;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class MetaPublisherService
{
    public function connectionStatus(int $clientId): array
    {
        $connection = $this->loadConnection($clientId);

        if ($connection === null) {
            return [
                'connected' => false,
                'page_name' => null,
                'instagram_username' => null,
                'connected_at' => null,
            ];
        }

        return [
            'connected' => true,
            'page_name' => $connection['page_name'] ?? null,
            'instagram_username' => $connection['instagram_username'] ?? null,
            'connected_at' => $connection['connected_at'] ?? null,
        ];
    }

    public function saveConnection(int $clientId, array $connection): void
    {
        Storage::disk('local')->makeDirectory('publisher/meta');

        Storage::disk('local')->put(
            $this->connectionPath($clientId),
            Crypt::encryptString(json_encode($connection, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE))
        );
    }

    public function disconnect(int $clientId): void
    {
        Storage::disk('local')->delete($this->connectionPath($clientId));
    }

    public function publish(ContentProject $project): array
    {
        if (! in_array($project->status, ['ready', 'scheduled'], true)) {
            throw new RuntimeException('O conteúdo precisa estar aprovado antes da publicação.');
        }

        if (! in_array($project->channel, ['instagram', 'facebook'], true)) {
            throw new RuntimeException('Publicação direta disponível apenas para Instagram e Facebook.');
        }

        $connection = $this->loadConnection((int) $project->client_id);

        if ($connection === null) {
            throw new RuntimeException('Nenhuma conta Meta publicadora está conectada para este cliente.');
        }

        $assetUrl = $this->ensureImageAsset($project);
        $caption = trim(implode("\n\n", array_filter([
            (string) $project->caption,
            (string) $project->cta,
            (string) $project->hashtags,
        ])));

        $baseUrl = 'https://graph.facebook.com';
        $version = trim((string) config('services.social_login.facebook.graph_version', 'v26.0'));
        $accessToken = trim((string) ($connection['access_token'] ?? ''));

        if ($accessToken === '') {
            throw new RuntimeException('A conexão Meta não possui credencial de publicação válida.');
        }

        if ($project->channel === 'instagram') {
            $instagramUserId = trim((string) ($connection['instagram_user_id'] ?? ''));

            if ($instagramUserId === '') {
                throw new RuntimeException('A Página conectada não possui uma conta profissional do Instagram vinculada.');
            }

            $create = Http::asForm()
                ->acceptJson()
                ->timeout(45)
                ->post($baseUrl.'/'.$version.'/'.$instagramUserId.'/media', [
                    'image_url' => $assetUrl,
                    'caption' => $caption,
                    'access_token' => $accessToken,
                ]);

            if (! $create->successful() || trim((string) $create->json('id')) === '') {
                throw new RuntimeException('Falha ao criar mídia no Instagram. HTTP '.$create->status().'.');
            }

            $containerId = trim((string) $create->json('id'));

            $publish = Http::asForm()
                ->acceptJson()
                ->timeout(45)
                ->post($baseUrl.'/'.$version.'/'.$instagramUserId.'/media_publish', [
                    'creation_id' => $containerId,
                    'access_token' => $accessToken,
                ]);

            if (! $publish->successful() || trim((string) $publish->json('id')) === '') {
                throw new RuntimeException('Falha ao publicar no Instagram. HTTP '.$publish->status().'.');
            }

            $result = [
                'provider' => 'meta',
                'channel' => 'instagram',
                'external_id' => trim((string) $publish->json('id')),
                'container_id' => $containerId,
                'asset_url' => $assetUrl,
            ];
        } else {
            $pageId = trim((string) ($connection['page_id'] ?? ''));

            if ($pageId === '') {
                throw new RuntimeException('A conexão Meta não possui uma Página do Facebook válida.');
            }

            $publish = Http::asForm()
                ->acceptJson()
                ->timeout(60)
                ->post($baseUrl.'/'.$version.'/'.$pageId.'/photos', [
                    'url' => $assetUrl,
                    'caption' => $caption,
                    'published' => 'true',
                    'access_token' => $accessToken,
                ]);

            if (! $publish->successful() || trim((string) $publish->json('id')) === '') {
                throw new RuntimeException('Falha ao publicar no Facebook. HTTP '.$publish->status().'.');
            }

            $result = [
                'provider' => 'meta',
                'channel' => 'facebook',
                'external_id' => trim((string) $publish->json('id')),
                'container_id' => null,
                'asset_url' => $assetUrl,
            ];
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

    public function publishDue(): int
    {
        $published = 0;

        ContentProject::query()
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->whereNull('published_at')
            ->orderBy('scheduled_at')
            ->limit(20)
            ->get()
            ->each(function (ContentProject $project) use (&$published): void {
                try {
                    $this->publish($project);
                    $published++;
                } catch (Throwable $exception) {
                    report($exception);
                }
            });

        return $published;
    }

    private function loadConnection(int $clientId): ?array
    {
        $path = $this->connectionPath($clientId);

        if (! Storage::disk('local')->exists($path)) {
            return null;
        }

        try {
            $decoded = json_decode(
                Crypt::decryptString(Storage::disk('local')->get($path)),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            return is_array($decoded) ? $decoded : null;
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function connectionPath(int $clientId): string
    {
        return 'publisher/meta/client-'.$clientId.'.enc';
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
                    'project_id' => (string) config('services.marketing_engine.project_id', 'vitrine-ai-social-enterprise'),
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
