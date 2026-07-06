<?php

namespace App\Filament\Widgets;

use App\Models\Brand;
use App\Models\Client;
use App\Models\ContentGeneration;
use App\Models\ContentProject;
use App\Models\PromptTemplate;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudioOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Clientes', Client::query()->count())
                ->description('Contas cadastradas')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Brand Kits', Brand::query()->count())
                ->description('Marcas configuradas')
                ->icon('heroicon-o-swatch'),

            Stat::make('Conteúdos', ContentProject::query()->count())
                ->description('Projetos criados')
                ->icon('heroicon-o-sparkles'),

            Stat::make('Gerações IA', ContentGeneration::query()->count())
                ->description('Histórico de IA')
                ->icon('heroicon-o-bolt'),

            Stat::make('Prompts IA', PromptTemplate::query()->count())
                ->description('Templates ativos')
                ->icon('heroicon-o-document-text'),
        ];
    }
}
