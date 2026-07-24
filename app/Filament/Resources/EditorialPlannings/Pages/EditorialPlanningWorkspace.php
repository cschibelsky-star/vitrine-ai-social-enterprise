<?php

namespace App\Filament\Resources\EditorialPlannings\Pages;

use App\Filament\Resources\EditorialPlannings\EditorialPlanningResource;
use App\Models\AiCreditTransaction;
use App\Models\ContentProject;
use App\Services\ContentLibraryService;
use App\Services\CreditService;
use App\Services\OpenAiContentService;
use App\Services\SocialContentPromptBuilder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class EditorialPlanningWorkspace extends Page
{
    use InteractsWithRecord;

    protected static string $resource = EditorialPlanningResource::class;
    protected string $view = 'filament.resources.editorial-plannings.pages.editorial-planning-workspace';

    public ?string $caption = null;
    public ?string $cta = null;
    public ?string $hashtags = null;
    public bool $generatingWithAi = false;

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

        Notification::make()->title('Produção iniciada')->success()->send();
    }

    public function generateWithAI(
        SocialContentPromptBuilder $promptBuilder,
        ContentLibraryService $contentLibraryService,
        CreditService $creditService,
        OpenAiContentService $openAiContentService,
    ): void {
        if ($this->generatingWithAi) {
            return;
        }

        $this->generatingWithAi = true;
        $transaction = null;

        try {
            if (! $this->record->contentProject) {
                $this->startProduction();
            }

            $libraryMatch = $contentLibraryService->bestReusableMatch($this->record);

            if ($libraryMatch) {
                $project = $contentLibraryService->reuse(
                    $libraryMatch['project'],
                    $this->record->contentProject,
                );

                $this->caption = $project->caption;
                $this->cta = $project->cta;
                $this->hashtags = $project->hashtags;

                Notification::make()
                    ->title('Conteúdo reutilizado da Biblioteca Inteligente')
                    ->body('Similaridade encontrada: '.number_format($libraryMatch['similarity'] * 100, 0).'% — nenhum crédito foi consumido.')
                    ->success()
                    ->send();

                return;
            }

            $cacheKey = $promptBuilder->cacheKey($this->record);
            $result = Cache::get($cacheKey);
            $fromCache = is_array($result);

            if (! $fromCache) {
                $transaction = $creditService->reserve(
                    $this->record->client,
                    'text',
                    'social_content_generation',
                    (string) Str::uuid(),
                    metadata: ['editorial_planning_id' => $this->record->getKey()],
                );

                $result = $openAiContentService->generate($promptBuilder->caption($this->record));
                Cache::put($cacheKey, $result, now()->addDays(30));
            }

            $this->caption = $result['caption'];
            $this->cta = $result['cta'];
            $this->hashtags = $result['hashtags'];

            $this->record->contentProject->update([
                'caption' => $this->caption,
                'cta' => $this->cta,
                'hashtags' => $this->hashtags,
                'generation_method' => $fromCache ? 'ai_cache' : 'ai',
                'status' => 'writing',
            ]);

            if ($transaction instanceof AiCreditTransaction) {
                $creditService->confirm(
                    $transaction,
                    'openai',
                    $result['model'] ?? null,
                    $result['estimated_cost'] ?? null,
                );
            }

            Notification::make()
                ->title($fromCache ? 'Conteúdo recuperado do cache' : 'Conteúdo gerado com IA')
                ->body($fromCache ? 'Nenhum crédito foi consumido.' : null)
                ->success()
                ->send();
        } catch (Throwable $exception) {
            if ($transaction instanceof AiCreditTransaction) {
                $creditService->rollback($transaction, ['error' => $exception->getMessage()]);
            }

            report($exception);

            Notification::make()
                ->title('Não foi possível gerar o conteúdo')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->generatingWithAi = false;
        }
    }

    public function saveDraft(): void
    {
        $project = $this->record->contentProject;

        if (! $project) {
            Notification::make()->title('Inicie a produção primeiro')->warning()->send();
            return;
        }

        $project->update([
            'caption' => $this->caption,
            'cta' => $this->cta,
            'hashtags' => $this->hashtags,
            'status' => 'writing',
        ]);

        Notification::make()->title('Rascunho salvo')->success()->send();
    }

    private function fillEditorFromProject(): void
    {
        $project = $this->record->contentProject;
        $this->caption = $project?->caption;
        $this->cta = $project?->cta;
        $this->hashtags = $project?->hashtags;
    }
}
