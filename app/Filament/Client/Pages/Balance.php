<?php

namespace App\Filament\Client\Pages;

class Balance extends BaseClientSection
{
    protected static ?string $navigationLabel = 'Plano e uso';
    protected static ?string $title = 'Plano e uso';
    protected static ?int $navigationSort = 50;
    protected static ?string $slug = 'consumo-saldo';
    public static string $sectionKey = 'balance';
}
