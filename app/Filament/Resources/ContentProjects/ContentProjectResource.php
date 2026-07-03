<?php

namespace App\Filament\Resources\ContentProjects;

use App\Filament\Resources\ContentProjects\Pages\CreateContentProject;
use App\Filament\Resources\ContentProjects\Pages\EditContentProject;
use App\Filament\Resources\ContentProjects\Pages\ListContentProjects;
use App\Filament\Resources\ContentProjects\Pages\ViewContentProject;
use App\Filament\Resources\ContentProjects\Schemas\ContentProjectForm;
use App\Filament\Resources\ContentProjects\Schemas\ContentProjectInfolist;
use App\Filament\Resources\ContentProjects\Tables\ContentProjectsTable;
use App\Models\ContentProject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentProjectResource extends Resource
{
    protected static ?string $model = ContentProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Criar Conteúdo';

    protected static ?string $modelLabel = 'Conteúdo';

    protected static ?string $pluralModelLabel = 'Conteúdos';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ContentProjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentProjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentProjectsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentProjects::route('/'),
            'create' => CreateContentProject::route('/create'),
            'view' => ViewContentProject::route('/{record}'),
            'edit' => EditContentProject::route('/{record}/edit'),
        ];
    }
}
