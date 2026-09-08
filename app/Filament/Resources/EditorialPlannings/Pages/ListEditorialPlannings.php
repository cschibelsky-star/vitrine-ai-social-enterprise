<?php

namespace App\Filament\Resources\EditorialPlannings\Pages;

use App\Filament\Resources\EditorialPlannings\EditorialPlanningResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEditorialPlannings extends ListRecords
{
    protected static string $resource = EditorialPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
