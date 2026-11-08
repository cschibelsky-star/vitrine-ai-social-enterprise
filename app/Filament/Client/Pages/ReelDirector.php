<?php

namespace App\Filament\Client\Pages;

use App\Models\ContentProject;
use App\Services\AI\ReelDirectorService;
use Filament\Pages\Page;

class ReelDirector extends Page
{
    protected static ?string $navigationLabel = 'Diretor de Reels IA';
    protected static ?string $title = 'Diretor de Reels IA';
    protected static ?int $navigationSort = 15;
    protected static ?string $slug = 'diretor-reels';

    protected string $view = 'filament.client.pages.reel-director';

    public function getViewData(): array
    {
        $clientId = (int) (auth()->user()?->client_id ?? 0);
        $projects = ContentProject::query()
            ->where('client_id', $clientId)
            ->where('format', 'reels')
            ->latest()
            ->limit(30)
            ->get();

        $projectId = (int) request()->query('project_id', 0);
        $selected = $projects->firstWhere('id', $projectId);
        $preview = null;

        if ($selected) {
            $brand = $selected->brand;
            if ($brand && (int) $brand->client_id !== $clientId) {
                $brand = null;
            }

            $preview = app(ReelDirectorService::class)->preview([
                'idea' => $selected->idea,
                'caption' => $selected->caption,
                'cta' => $selected->cta,
                'forbidden_words' => $brand?->forbidden_words ?? [],
                'target_seconds' => 30,
            ]);
        }

        return compact('projects', 'selected', 'preview');
    }
}
