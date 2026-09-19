<?php

namespace App\Filament\Client\Pages;

class Affiliates extends BaseClientSection
{
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationLabel = 'Programa de Afiliados';
    protected static ?string $title = 'Programa de Afiliados';
    protected static ?int $navigationSort = 60;
    protected static ?string $slug = 'afiliados';
    public static string $sectionKey = 'affiliates';
}
