<?php

namespace App\Filament\Client\Pages;

use App\Filament\Client\Widgets\ClientCommandCenter;
use Filament\Pages\Dashboard;

class ClientDashboard extends Dashboard
{
    protected static ?string $navigationLabel = 'Painel';

    protected static ?string $title = 'Painel';

    protected static ?int $navigationSort = -10;

    public function getWidgets(): array
    {
        return [
            ClientCommandCenter::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 1;
    }
}
