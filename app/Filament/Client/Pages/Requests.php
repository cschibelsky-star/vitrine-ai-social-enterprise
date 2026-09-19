<?php

namespace App\Filament\Client\Pages;

class Requests extends BaseClientSection
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'Solicitações';
    protected static ?string $title = 'Solicitações';
    protected static ?int $navigationSort = 50;
    protected static ?string $slug = 'solicitacoes';
    public static string $sectionKey = 'requests';
}
