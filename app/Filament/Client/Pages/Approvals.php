<?php

namespace App\Filament\Client\Pages;

class Approvals extends BaseClientSection
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'Aprovações';
    protected static ?string $title = 'Aprovações';
    protected static ?int $navigationSort = 30;
    protected static ?string $slug = 'aprovacoes';
    public static string $sectionKey = 'approvals';
}
