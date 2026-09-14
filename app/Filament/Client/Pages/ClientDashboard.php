<?php

namespace App\Filament\Client\Pages;

use Filament\Pages\Dashboard;

class ClientDashboard extends Dashboard
{
    protected static ?string $navigationLabel = 'Painel';

    protected static ?string $title = 'Painel';

    protected static ?int $navigationSort = -10;
}
