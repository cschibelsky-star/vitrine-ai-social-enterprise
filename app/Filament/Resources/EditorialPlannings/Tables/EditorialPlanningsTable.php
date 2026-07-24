<?php

namespace App\Filament\Resources\EditorialPlannings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EditorialPlanningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('planned_for')
            ->columns([
                TextColumn::make('planned_for')->label('Data')->date('d/m/Y')->sortable(),
                TextColumn::make('client.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('theme')->label('Tema')->searchable()->wrap(),
                TextColumn::make('objective')->label('Objetivo')->toggleable(),
                TextColumn::make('channel')->label('Canal')->badge(),
                TextColumn::make('format')->label('Formato')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('priority')->label('Prioridade')->formatStateUsing(fn (int|string|null $state): string => match ((int) $state) {
                    1 => 'Urgente',
                    2 => 'Alta',
                    4 => 'Baixa',
                    default => 'Normal',
                })->badge(),
            ])
            ->filters([
                SelectFilter::make('client_id')->label('Cliente')->relationship('client', 'name')->searchable()->preload(),
                SelectFilter::make('status')->label('Status')->options([
                    'planned' => 'Planejado',
                    'writing' => 'Em produção',
                    'caption_ready' => 'Legenda pronta',
                    'caption_approved' => 'Legenda aprovada',
                    'image_ready' => 'Imagem pronta',
                    'image_approved' => 'Imagem aprovada',
                    'scheduled' => 'Agendado',
                    'published' => 'Publicado',
                    'archived' => 'Arquivado',
                ]),
                SelectFilter::make('channel')->label('Canal')->options([
                    'instagram' => 'Instagram',
                    'facebook' => 'Facebook',
                    'linkedin' => 'LinkedIn',
                    'threads' => 'Threads',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
