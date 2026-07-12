<?php

namespace App\Filament\Widgets;

use App\Models\ContentProject;
use Filament\Widgets\Widget;

class RecentContent extends Widget
{
    protected string $view = 'filament.widgets.recent-content';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    protected function getViewData(): array
    {
        return [
            'projects' => ContentProject::query()
                ->with(['client', 'brand'])
                ->latest()
                ->limit(6)
                ->get(),
        ];
    }
}
