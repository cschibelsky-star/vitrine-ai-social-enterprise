<?php

namespace App\Filament\Resources\PromptTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PromptTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Prompt')->searchable()->sortable(),
                TextColumn::make('category')->label('Categoria')->badge(),
                TextColumn::make('objective')->label('Objetivo')->badge(),
                TextColumn::make('format')->label('Formato')->badge(),
                IconColumn::make('is_active')->label('Ativo')->boolean(),
                TextColumn::make('updated_at')->label('Atualizado')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        'Venda' => 'Venda',
                        'Educacional' => 'Educacional',
                        'Autoridade' => 'Autoridade',
                        'Engajamento' => 'Engajamento',
                        'Comunidade' => 'Comunidade',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
