<?php

namespace App\Filament\Client\Pages;

class Account extends BaseClientSection
{
    protected static ?string $navigationLabel = 'Conta';
    protected static ?string $title = 'Conta';
    protected static ?int $navigationSort = 70;
    protected static ?string $slug = 'conta';
    public static string $sectionKey = 'account';
}
