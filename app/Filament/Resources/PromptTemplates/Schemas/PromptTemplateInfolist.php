<?php

namespace App\Filament\Resources\PromptTemplates\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PromptTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Nome'),
                TextEntry::make('category')->label('Categoria'),
                TextEntry::make('objective')->label('Objetivo'),
                TextEntry::make('format')->label('Formato'),
                TextEntry::make('prompt_text')->label('Prompt')->columnSpanFull(),
                IconEntry::make('is_active')->label('Ativo')->boolean(),
            ]);
    }
}
