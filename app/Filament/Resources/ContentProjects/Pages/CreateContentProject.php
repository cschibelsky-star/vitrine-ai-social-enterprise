<?php

namespace App\Filament\Resources\ContentProjects\Pages;

use App\Filament\Resources\ContentProjects\ContentProjectResource;
use App\Services\ContentGenerationService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateContentProject extends CreateRecord
{
    protected static string $resource = ContentProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'draft';
        $data['generation_method'] = $data['generation_method'] ?? 'from_scratch';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $record = parent::handleRecordCreation($data);

            app(ContentGenerationService::class)->generate($record, Auth::user());

            return $record;
        });
    }

    protected function afterCreate(): void
    {
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
