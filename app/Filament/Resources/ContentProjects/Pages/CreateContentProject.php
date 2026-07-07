<?php

namespace App\Filament\Resources\ContentProjects\Pages;

use App\Filament\Resources\ContentProjects\ContentProjectResource;
use App\Services\AI\AiContentService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateContentProject extends CreateRecord
{
    protected static string $resource = ContentProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'draft';
        $data['generation_method'] = $data['generation_method'] ?? 'from_scratch';

        return $data;
    }

    protected function afterCreate(): void
    {
        app(AiContentService::class)->generateProject($this->record);

        Notification::make()
            ->title('Conteudo gerado com IA')
            ->body('Titulo, legenda, CTA, hashtags, score e slides foram gerados automaticamente.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
