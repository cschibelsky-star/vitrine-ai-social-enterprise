<?php

namespace App\Filament\Resources\ContentProjects\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContentProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->label('Título'),
                TextEntry::make('client.name')->label('Cliente'),
                TextEntry::make('brand.name')->label('Marca'),
                TextEntry::make('objective')->label('Objetivo')->badge(),
                TextEntry::make('format')->label('Formato')->badge(),
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('score')->label('Score'),
                TextEntry::make('idea')->label('Ideia')->columnSpanFull(),
                TextEntry::make('caption')->label('Legenda')->columnSpanFull(),
                TextEntry::make('cta')->label('CTA')->columnSpanFull(),
                TextEntry::make('hashtags')->label('Hashtags')->columnSpanFull(),
            ]);
    }
}
