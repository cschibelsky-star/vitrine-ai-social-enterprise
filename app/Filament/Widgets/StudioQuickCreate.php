<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class StudioQuickCreate extends Widget
{
    protected string $view = 'filament.widgets.studio-quick-create';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;
}
