<?php

namespace App\Filament\Resources\Brands\Schemas;

use App\Models\Client;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identidade da marca')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('client_id')
                                ->label('Cliente')
                                ->options(fn () => Client::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            TextInput::make('name')
                                ->label('Nome da marca')
                                ->required()
                                ->maxLength(255),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Ativo',
                                    'inactive' => 'Inativo',
                                ])
                                ->default('active')
                                ->required(),
                        ]),

                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('brands/logos'),
                    ]),

                Section::make('Diretrizes de comunicação')
                    ->schema([
                        Textarea::make('tone_of_voice')
                            ->label('Tom de voz')
                            ->rows(4)
                            ->placeholder('Ex.: próximo, profissional, acolhedor, direto...'),

                        Textarea::make('target_audience')
                            ->label('Público-alvo')
                            ->rows(4),

                        TagsInput::make('preferred_words')
                            ->label('Palavras preferidas'),

                        TagsInput::make('forbidden_words')
                            ->label('Palavras proibidas'),

                        KeyValue::make('main_colors')
                            ->label('Cores principais')
                            ->keyLabel('Nome')
                            ->valueLabel('Hexadecimal'),

                        Textarea::make('notes')
                            ->label('Observações internas')
                            ->rows(3),
                    ]),
            ]);
    }
}
