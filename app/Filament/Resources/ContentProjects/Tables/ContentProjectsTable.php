<?php

namespace App\Filament\Resources\ContentProjects\Tables;

use App\Services\AI\AiContentService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContentProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Título')->searchable()->sortable()->placeholder('Sem título'),
                TextColumn::make('client.name')->label('Cliente')->searchable(),
                TextColumn::make('brand.name')->label('Marca')->searchable(),
                TextColumn::make('objective')->label('Objetivo')->badge(),
                TextColumn::make('format')->label('Formato')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('score')->label('Score')->sortable(),
                TextColumn::make('updated_at')->label('Atualizado')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('objective')
                    ->label('Objetivo')
                    ->options([
                        'engagement' => 'Engajamento',
                        'sales' => 'Venda',
                        'education' => 'Educacional',
                        'authority' => 'Autoridade',
                        'community' => 'Comunidade',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Rascunho',
                        'editing' => 'Em edição',
                        'ready' => 'Pronto',
                        'scheduled' => 'Agendado',
                        'published' => 'Publicado',
                    ]),
            ])
            ->recordActions([
                Action::make('generate')
                    ->label('Gerar com IA')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->action(function ($record) {
                        app(AiContentService::class)->generateProject($record);

                        Notification::make()
                            ->title('Conteúdo gerado com IA')
                            ->success()
                            ->send();
                    }),

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
