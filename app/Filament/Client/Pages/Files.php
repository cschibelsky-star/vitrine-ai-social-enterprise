<?php

namespace App\Filament\Client\Pages;

class Files extends BaseClientSection
{
    protected static ?string $navigationLabel = 'Arquivos';
    protected static ?string $title = 'Arquivos';
    protected static ?int $navigationSort = 70;
    protected static ?string $slug = 'arquivos';
    public static string $sectionKey = 'files';
}
