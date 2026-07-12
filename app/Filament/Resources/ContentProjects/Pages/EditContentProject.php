<?php

namespace App\Filament\Resources\ContentProjects\Pages;

use App\Filament\Resources\ContentProjects\ContentProjectResource;
use App\Filament\Widgets\ContentStudioPreview;
use App\Services\AI\AiContentService;
use App\Services\AI\ContentRefinementService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditContentProject extends EditRecord
{
    protected static string $resource = ContentProjectResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ContentStudioPreview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Gerar novamente')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->action(function () {
                    app(AiContentService::class)->generateProject($this->record);
                    $this->refreshStudio();
                    $this->notify('Conteúdo gerado novamente');
                }),

            Action::make('improve')
                ->label('Melhorar')
                ->icon('heroicon-o-bolt')
                ->action(fn () => $this->refine('improve', 'Conteúdo melhorado')),

            Action::make('shorten')
                ->label('Encurtar')
                ->icon('heroicon-o-arrows-pointing-in')
                ->color('gray')
                ->action(fn () => $this->refine('shorten', 'Conteúdo encurtado')),

            Action::make('expand')
                ->label('Expandir')
                ->icon('heroicon-o-arrows-pointing-out')
                ->color('gray')
                ->action(fn () => $this->refine('expand', 'Conteúdo expandido')),

            Action::make('persuasive')
                ->label('Mais persuasivo')
                ->icon('heroicon-o-megaphone')
                ->color('warning')
                ->action(fn () => $this->refine('persuasive', 'Tom persuasivo aplicado')),

            Action::make('emotional')
                ->label('Mais emocional')
                ->icon('heroicon-o-heart')
                ->color('danger')
                ->action(fn () => $this->refine('emotional', 'Tom emocional aplicado')),

            Action::make('professional')
                ->label('Mais profissional')
                ->icon('heroicon-o-briefcase')
                ->color('info')
                ->action(fn () => $this->refine('professional', 'Tom profissional aplicado')),

            Action::make('image')
                ->label('Gerar imagem')
                ->icon('heroicon-o-photo')
                ->color('gray')
                ->disabled()
                ->tooltip('Será ativado na BUILD 010'),

            Action::make('schedule')
                ->label('Agendar')
                ->icon('heroicon-o-calendar-days')
                ->color('gray')
                ->disabled()
                ->tooltip('Será ativado na BUILD 011'),

            Action::make('ready')
                ->label('Marcar como pronto')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update(['status' => 'ready']);
                    $this->refreshStudio();
                    $this->notify('Conteúdo marcado como pronto');
                }),
        ];
    }

    private function refine(string $action, string $message): void
    {
        app(ContentRefinementService::class)->refine($this->record, $action);
        $this->refreshStudio();
        $this->notify($message);
    }

    private function refreshStudio(): void
    {
        $this->record->refresh();
        $this->refreshFormData([
            'title',
            'caption',
            'cta',
            'hashtags',
            'score',
            'status',
        ]);

        $this->dispatch('$refresh');
    }

    private function notify(string $title): void
    {
        Notification::make()
            ->title($title)
            ->success()
            ->send();
    }
}
