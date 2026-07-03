<?php

namespace App\Filament\Resources\PromptTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PromptTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Configuração do prompt')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label('Nome')->required(),
                            Select::make('category')
                                ->label('Categoria')
                                ->options([
                                    'Venda' => 'Venda',
                                    'Educacional' => 'Educacional',
                                    'Autoridade' => 'Autoridade',
                                    'Engajamento' => 'Engajamento',
                                    'Comunidade' => 'Comunidade',
                                ])
                                ->required(),

                            Select::make('objective')
                                ->label('Objetivo')
                                ->options([
                                    'sales' => 'Venda',
                                    'education' => 'Educacional',
                                    'authority' => 'Autoridade',
                                    'engagement' => 'Engajamento',
                                    'community' => 'Comunidade',
                                ]),

                            Select::make('format')
                                ->label('Formato')
                                ->options([
                                    'post_portrait' => 'Post Portrait',
                                    'carousel_portrait' => 'Carrossel Portrait',
                                    'stories' => 'Stories',
                                    'reels' => 'Reels',
                                    'facebook_post' => 'Facebook',
                                ]),

                            Select::make('is_active')
                                ->label('Ativo?')
                                ->options([
                                    1 => 'Sim',
                                    0 => 'Não',
                                ])
                                ->default(1)
                                ->required(),
                        ]),

                        Textarea::make('prompt_text')
                            ->label('Texto do prompt')
                            ->rows(8)
                            ->required(),

                        TagsInput::make('variables')
                            ->label('Variáveis'),
                    ]),
            ]);
    }
}
