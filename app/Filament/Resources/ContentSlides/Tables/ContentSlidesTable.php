<?php

namespace App\Filament\Resources\ContentSlides\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentSlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contentProject.title')
                    ->label('Conteudo')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sem titulo'),

                TextColumn::make('slide_number')
                    ->label('Slide')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('layout_type')
                    ->label('Layout')
                    ->badge(),

                TextColumn::make('body')
                    ->label('Texto')
                    ->limit(80),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
