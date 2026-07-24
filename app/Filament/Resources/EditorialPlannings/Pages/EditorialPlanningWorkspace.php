<?php

namespace App\Filament\Resources\EditorialPlannings\Pages;

use App\Filament\Resources\EditorialPlannings\EditorialPlanningResource;
use App\Models\ContentProject;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class EditorialPlanningWorkspace extends Page
{
    use InteractsWithRecord;

    protected static string $resource = EditorialPlanningResource::class;

    protected string $view = 'filament.resources.editorial-plannings.pages.editorial-planning-workspace';

    public ?string $caption = null;

    public ?string $cta = null;

    public ?string $hashtags = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load(['client', 'contentProject']);

        $this->fillEditorFromProject();
    }

    public function getTitle(): string
    {
        return 'Workspace de Produção';
    }

    public function getSubheading(): ?string
    {
        return $this->record->theme;
    }

    public function startProduction(): void
    {
        if ($this->record->contentProject) {
            $this->fillEditorFromProject();

            return;
        }

        DB::transaction(function (): void {
            $project = ContentProject::query()->create([
                'client_id' => $this->record->client_id,
                'title' => $this->record->theme,
                'idea' => $this->record->notes,
                'objective' => $this->record->objective,
                'format' => $this->record->format,
                'channel' => $this->record->channel,
                'content_type' => $this->record->format,
                'generation_method' => 'manual',
                'status' => 'writing',
                'scheduled_at' => $this->record->planned_for,
                'created_by' => auth()->id(),
            ]);

            $this->record->update([
                'content_project_id' => $project->getKey(),
                'status' => 'writing',
            ]);
        });

        $this->record->refresh()->load(['client', 'contentProject']);
        $this->fillEditorFromProject();

        Notification::make()
            ->title('Produção iniciada')
            ->body('O workspace foi vinculado a um novo projeto de conteúdo.')
            ->success()
            ->send();
    }

    public function saveDraft(): void
    {
        $project = $this->record->contentProject;

        if (! $project) {
            Notification::make()
                ->title('Inicie a produção primeiro')
                ->warning()
                ->send();

            return;
        }

        $project->update([
            'caption' => $this->caption,
            'cta' => $this->cta,
            'hashtags' => $this->hashtags,
            'status' => 'writing',
        ]);

        Notification::make()
            ->title('Rascunho salvo')
            ->success()
            ->send();
    }

    private function fillEditorFromProject(): void
    {
        $project = $this->record->contentProject;

        $this->caption = $project?->caption;
        $this->cta = $project?->cta;
        $this->hashtags = $project?->hashtags;
    }
}
