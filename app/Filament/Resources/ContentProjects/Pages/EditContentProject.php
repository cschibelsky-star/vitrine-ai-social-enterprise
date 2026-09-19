<?php

namespace App\Filament\Resources\ContentProjects\Pages;

use App\Filament\Resources\ContentProjects\ContentProjectResource;
use App\Filament\Widgets\ContentStudioPreview;
use App\Services\AI\AiContentService;
use App\Models\ContentGeneration;
use App\Services\AI\ContentRefinementService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

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
                ->color('primary')
                ->action(function () {
                    $this->record->refresh()->loadMissing('brand');

                    $url = trim((string) config('services.marketing_engine.url', ''));
                    $token = trim((string) config('services.marketing_engine.token', ''));
                    $projectId = trim((string) config('services.marketing_engine.project_id', 'vitrine-ai-social-enterprise'));

                    if ($url === '' || $token === '') {
                        throw new RuntimeException('Marketing IA Engine não está configurado para geração de imagem.');
                    }

                    $response = Http::acceptJson()
                        ->asJson()
                        ->withToken($token)
                        ->timeout(max(30, (int) config('services.marketing_engine.timeout', 150)))
                        ->post($url, [
                            'project_id' => $projectId,
                            'brand' => (string) ($this->record->brand?->name ?: 'Marca do cliente'),
                            'idea' => (string) $this->record->idea,
                            'objective' => (string) $this->record->objective,
                            'channel' => (string) $this->record->channel,
                            'format' => (string) $this->record->format,
                            'title' => (string) $this->record->title,
                            'caption' => (string) $this->record->caption,
                            'cta' => (string) $this->record->cta,
                        ]);

                    if (! $response->successful() || ! $response->json('ok')) {
                        throw new RuntimeException('O Marketing IA Engine não concluiu a geração da imagem.');
                    }

                    $binary = base64_decode((string) $response->json('image_base64'), true);
                    if ($binary === false || $binary === '') {
                        throw new RuntimeException('O Marketing IA Engine retornou uma imagem inválida.');
                    }

                    $mime = (string) $response->json('mime_type', 'image/png');
                    $extension = str_contains($mime, 'jpeg') || str_contains($mime, 'jpg') ? 'jpg' : (str_contains($mime, 'webp') ? 'webp' : 'png');
                    $path = 'generated/social/'.now()->format('Y/m/d').'/'.Str::uuid().'.'.$extension;

                    if (! Storage::disk('public')->put($path, $binary)) {
                        throw new RuntimeException('Não foi possível salvar a imagem gerada.');
                    }

                    ContentGeneration::create([
                        'content_project_id' => $this->record->id,
                        'provider' => 'marketing-ia-engine',
                        'model' => (string) $response->json('model', 'gemini-image'),
                        'input_data' => [
                            'idea' => $this->record->idea,
                            'objective' => $this->record->objective,
                            'channel' => $this->record->channel,
                            'format' => $this->record->format,
                        ],
                        'output_data' => [
                            'asset_path' => $path,
                            'asset_url' => Storage::disk('public')->url($path),
                            'mime_type' => $mime,
                        ],
                        'metadata' => [
                            'type' => 'image_generation',
                            'action' => 'Imagem gerada pelo Marketing IA',
                            'marketing_generation_id' => $response->json('generation_id'),
                            'engine' => 'marketing-ia',
                            'provider' => 'google',
                        ],
                        'latency_ms' => 0,
                    ]);

                    $this->record->update(['status' => 'editing']);
                    $this->refreshStudio();
                    $this->notify('Imagem gerada pelo Marketing IA');
                }),

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
