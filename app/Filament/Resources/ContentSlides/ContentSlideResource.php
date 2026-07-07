<?php

namespace App\Filament\Resources\ContentSlides;

use App\Filament\Resources\ContentSlides\Pages\ListContentSlides;
use App\Filament\Resources\ContentSlides\Pages\ViewContentSlide;
use App\Filament\Resources\ContentSlides\Tables\ContentSlidesTable;
use App\Models\ContentSlide;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentSlideResource extends Resource
{
    protected static ?string $model = ContentSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Slides Gerados';

    protected static ?string $modelLabel = 'Slide Gerado';

    protected static ?string $pluralModelLabel = 'Slides Gerados';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return ContentSlidesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentSlides::route('/'),
            'view' => ViewContentSlide::route('/{record}'),
        ];
    }
}
