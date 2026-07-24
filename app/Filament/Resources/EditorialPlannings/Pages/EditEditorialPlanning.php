<?php

namespace App\Filament\Resources\EditorialPlannings\Pages;

use App\Filament\Resources\EditorialPlannings\EditorialPlanningResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEditorialPlanning extends EditRecord
{
    protected static string $resource = EditorialPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}"}