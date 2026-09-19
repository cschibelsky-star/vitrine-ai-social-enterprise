<?php

namespace App\Filament\Widgets;

use App\Models\ContentProject;
use Filament\Widgets\Widget;

class ContentStudioPreview extends Widget
{
    protected string $view = 'filament.widgets.content-studio-preview';

    protected int|string|array $columnSpan = 'full';

    public ?ContentProject $record = null;

    protected function getViewData(): array
    {
        $project = $this->record?->loadMissing(['client', 'brand', 'slides', 'generations']);

        $versions = $project?->generations()->latest()->limit(8)->get() ?? collect();
        $imageGeneration = $versions->first(function ($version) {
            return (string) data_get($version->metadata, 'type') === 'image_generation'
                && filled(data_get($version->output_data, 'asset_url'));
        });
        $videoGeneration = $versions->first(function ($version) {
            return (string) data_get($version->metadata, 'type') === 'video_generation'
                && filled(data_get($version->output_data, 'asset_url'));
        });

        return [
            'project' => $project,
            'versions' => $versions,
            'generatedImageUrl' => $imageGeneration ? (string) data_get($imageGeneration->output_data, 'asset_url') : null,
            'generatedVideoUrl' => $videoGeneration ? (string) data_get($videoGeneration->output_data, 'asset_url') : null,
        ];
    }
}
