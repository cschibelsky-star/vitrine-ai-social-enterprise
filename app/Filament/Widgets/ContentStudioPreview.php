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

        return [
            'project' => $project,
            'versions' => $project?->generations()->latest()->limit(8)->get() ?? collect(),
        ];
    }
}
