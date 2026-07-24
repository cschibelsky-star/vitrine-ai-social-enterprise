<?php

namespace App\Filament\Resources\EditorialPlannings;

use App\Filament\Resources\EditorialPlannings\Pages\CreateEditorialPlanning;
use App\Filament\Resources\EditorialPlannings\Pages\EditEditorialPlanning;
use App\Filament\Resources\EditorialPlannings\Pages\ListEditorialPlannings;
use App\Filament\Resources\EditorialPlannings\Schemas\EditorialPlanningForm;
use App\Filament\Resources\EditorialPlannings\Tables\EditorialPlanningsTable;
use App\Models\EditorialPlanning;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EditorialPlanningResource extends Resource
{
    protected static ?string $model = EditorialPlanning::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?string $navigationLabel = 'Planejamento Editorial';
    protected static ?string $modelLabel = 'item editorial';
    protected static ?string $pluralModelLabel = 'planejamento editorial';
    protected static ?string $recordTitleAttribute = 'theme';

    public static function form(Schema $schema): Schema
    {
        return EditorialPlanningForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EditorialPlanningsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEditorialPlannings::route('/'),
            'create' => CreateEditorialPlanning::route('/create'),
            'edit' => EditEditorialPlanning::route('/{record}/edit'),
        ];
    }
}
