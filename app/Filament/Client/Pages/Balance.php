<?php

namespace App\Filament\Client\Pages;

class Balance extends BaseClientSection
{
    protected static ?string $navigationLabel = 'Consumo e Saldo';
    protected static ?string $title = 'Consumo e Saldo';
    protected static ?int $navigationSort = 50;
    protected static ?string $slug = 'consumo-saldo';
    public static string $sectionKey = 'balance';
}
