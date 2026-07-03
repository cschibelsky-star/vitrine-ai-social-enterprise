<?php

namespace App\Filament\Resources\PromptTemplates;

use App\Filament\Resources\PromptTemplates\Pages\CreatePromptTemplate;
use App\Filament\Resources\PromptTemplates\Pages\EditPromptTemplate;
use App\Filament\Resources\PromptTemplates\Pages\ListPromptTemplates;
use App\Filament\Resources\PromptTemplates\Pages\ViewPromptTemplate;
use App\Filament\Resources\PromptTemplates\Schemas\PromptTemplateForm;
use App\Filament\Resources\PromptTemplates\Schemas\PromptTemplateInfolist;
use App\Filament\Resources\PromptTemplates\Tables\PromptTemplatesTable;
use App\Models\PromptTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PromptTemplateResource extends Resource
{
    protected static ?string $model = PromptTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Prompts IA';

    protected static ?string $modelLabel = 'Prompt IA';

    protected static ?string $pluralModelLabel = 'Prompts IA';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PromptTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PromptTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromptTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromptTemplates::route('/'),
            'create' => CreatePromptTemplate::route('/create'),
            'view' => ViewPromptTemplate::route('/{record}'),
            'edit' => EditPromptTemplate::route('/{record}/edit'),
        ];
    }
}
