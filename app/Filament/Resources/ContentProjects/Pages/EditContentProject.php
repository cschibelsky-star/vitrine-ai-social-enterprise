<?php

namespace App\Filament\Resources\ContentProjects\Pages;

use App\Filament\Resources\ContentProjects\ContentProjectResource;
use App\Services\AI\AiContentService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditContentProject extends EditRecord
{
    protected static string $resource = ContentProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Gerar novamente')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->action(function () {
                    app(AiContentService::class)->generateProject($this->record);

                    $this->refreshFormData([
                        'title',
                        'caption',
                        'cta',
                        'hashtags',
                        'score',
                        'status',
                    ]);

                    Notification::make()
                        ->title('Conteudo gerado novamente')
                        ->success()
                        ->send();
                }),

            Action::make('ready')
                ->label('Marcar como pronto')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update(['status' => 'ready']);
                    $this->refreshFormData(['status']);

                    Notification::make()
                        ->title('Conteudo marcado como pronto')
                        ->success()
                        ->send();
                }),
        ];
    }
}
