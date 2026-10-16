<?php
namespace App\Services\Publishing;
use App\Models\ContentProject;
use RuntimeException;
class PublicationContract
{
    public function placement(string $format): string
    {
        return match ($format) {
            'post_portrait', 'facebook_post', 'feed' => 'feed',
            'stories' => 'stories',
            'reels' => 'reels',
            default => throw new RuntimeException('Formato não suportado para publicação direta.'),
        };
    }
    public function fingerprint(ContentProject $project): string
    {
        return hash('sha256', json_encode([
            $project->client_id, $project->channel, $project->format,
            $project->publishing_asset, $project->caption, $project->cta, $project->hashtags,
        ], JSON_THROW_ON_ERROR));
    }
    public function payload(ContentProject $project): array
    {
        if (! in_array($project->channel, ['instagram', 'facebook'], true)) {
            throw new RuntimeException('Canal não suportado.');
        }
        $placement = $this->placement((string) $project->format);
        $asset = (array) $project->publishing_asset;
        $mime = $asset['mime'] ?? '';
        if (! in_array($mime, ['image/jpeg', 'image/png', 'video/mp4'], true)
            || empty($asset['path']) || empty($asset['sha256'])) {
            throw new RuntimeException('Anexe um arquivo final validado antes de aprovar.');
        }
        if ($placement === 'reels' && $mime !== 'video/mp4') {
            throw new RuntimeException('Reels exige vídeo MP4.');
        }
        if ($placement === 'feed' && $mime === 'video/mp4') {
            throw new RuntimeException('Use Reels para este vídeo.');
        }
        if ($project->channel === 'instagram' && $mime === 'image/png') {
            throw new RuntimeException('Prepare a cópia JPEG antes da aprovação do Instagram.');
        }
        return [
            'contract_version' => 2,
            'client_id' => (int) $project->client_id,
            'content_project_id' => (int) $project->id,
            'channel' => $project->channel,
            'placement' => $placement,
            'type' => $mime === 'video/mp4' ? 'video' : 'image',
            'mime_type' => $mime,
            'asset_sha256' => $asset['sha256'],
            'caption' => $placement === 'stories' ? '' : trim(implode("\n\n", array_filter([
                $project->caption, $project->cta, $project->hashtags,
            ]))),
        ];
    }
}
