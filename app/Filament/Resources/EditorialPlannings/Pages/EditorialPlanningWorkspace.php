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

    /** @var array<int, array{id:int,title:string,channel:?string,format:?string,similarity:int,caption:string}> */
    public array $libraryMatches = [];

    public string $generationStage = 'ready';
    public ?string $generationSource = null;
    public ?string $generationMessage = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->reloadWorkspaceData();
        $this->fillEditorFromProject();
        $this->refreshLibraryMatches();
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

        $this->record->refresh();
        $this->reloadWorkspaceData();
        $this->fillEditorFromProject();
        $this->refreshLibraryMatches();

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
        $this->generationStage = 'searching_library';
        $this->generationSource = null;
        $this->generationMessage = 'Consultando conteúdos semelhantes.';
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
                $this->generationStage = 'completed';
                $this->generationSource = 'library';
                $this->generationMessage = 'Conteúdo reutilizado da Biblioteca Inteligente sem consumo de créditos.';
                $this->refreshLibraryMatches();

                Notification::make()
                    ->title('Conteúdo reutilizado da Biblioteca Inteligente')
                    ->body('Similaridade encontrada: '.number_format($libraryMatch['similarity'] * 100, 0).'% — nenhum crédito foi consumido.')
                    ->success()
                    ->send();

                return;
            }

            $this->generationStage = 'checking_cache';
            $this->generationMessage = 'Nenhuma reutilização automática encontrada. Verificando o cache.';

            $cacheKey = $promptBuilder->cacheKey($this->record);
            $result = Cache::get($cacheKey);
            $fromCache = is_array($result);

            if (! $fromCache) {
                $this->generationStage = 'generating_ai';
                $this->generationMessage = 'Gerando novo conteúdo com inteligência artificial.';

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

            $this->generationStage = 'completed';
            $this->generationSource = $fromCache ? 'cache' : 'ai';
            $this->generationMessage = $fromCache
                ? 'Conteúdo recuperado do cache sem consumo de créditos.'
                : 'Novo conteúdo gerado e salvo no projeto.';
            $this->refreshLibraryMatches();

            Notification::make()
                ->title($fromCache ? 'Conteúdo recuperado do cache' : 'Conteúdo gerado com IA')
                ->body($fromCache ? 'Nenhum crédito foi consumido.' : null)
                ->success()
                ->send();
        } catch (Throwable $exception) {
            if ($transaction instanceof AiCreditTransaction) {
                $creditService->rollback($transaction, ['error' => $exception->getMessage()]);
            }

            $this->generationStage = 'failed';
            $this->generationSource = null;
            $this->generationMessage = 'A geração não foi concluída. Revise a mensagem de erro e tente novamente.';

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

        $this->generationStage = 'completed';
        $this->generationSource = 'manual';
        $this->generationMessage = 'Rascunho salvo manualmente.';
        $this->refreshLibraryMatches();

        Notification::make()->title('Rascunho salvo')->success()->send();
    }

    private function reloadWorkspaceData(): void
    {
        $this->record->load([
            'client.activeBrand',
            'client.aiCreditWallet',
            'contentProject',
        ]);
    }

    private function refreshLibraryMatches(): void
    {
        $this->libraryMatches = app(ContentLibraryService::class)
            ->matches($this->record, 5)
            ->map(fn (array $match): array => [
                'id' => (int) $match['project']->getKey(),
                'title' => $match['project']->title ?: 'Conteúdo sem título',
                'channel' => $match['project']->channel,
                'format' => $match['project']->format,
                'similarity' => (int) round($match['similarity'] * 100),
                'caption' => Str::limit((string) $match['project']->caption, 150),
            ])
            ->all();
    }

    private function fillEditorFromProject(): void
    {
        $project = $this->record->contentProject;
        $this->caption = $project?->caption;
        $this->cta = $project?->cta;
        $this->hashtags = $project?->hashtags;
    }
}
