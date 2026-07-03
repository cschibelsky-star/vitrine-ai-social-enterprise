<?php

namespace App\Filament\Resources\ContentProjects\Schemas;

use App\Models\Brand;
use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Briefing')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('client_id')
                                ->label('Cliente')
                                ->options(fn () => Client::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable(),

                            Select::make('brand_id')
                                ->label('Brand Kit')
                                ->options(fn () => Brand::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable(),

                            Select::make('objective')
                                ->label('Objetivo')
                                ->options([
                                    'engagement' => 'Engajar',
                                    'sales' => 'Vender',
                                    'education' => 'Educar',
                                    'authority' => 'Autoridade',
                                    'community' => 'Comunidade',
                                ])
                                ->default('engagement')
                                ->required(),

                            Select::make('format')
                                ->label('Formato')
                                ->options([
                                    'post_portrait' => 'Post Portrait',
                                    'carousel_portrait' => 'Carrossel Portrait',
                                    'stories' => 'Stories',
                                    'reels' => 'Reels',
                                    'facebook_post' => 'Facebook',
                                ])
                                ->default('post_portrait')
                                ->required(),

                            Select::make('channel')
                                ->label('Canal')
                                ->options([
                                    'instagram' => 'Instagram',
                                    'facebook' => 'Facebook',
                                ])
                                ->default('instagram')
                                ->required(),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'draft' => 'Rascunho',
                                    'editing' => 'Em edição',
                                    'ready' => 'Pronto',
                                    'scheduled' => 'Agendado',
                                    'published' => 'Publicado',
                                ])
                                ->default('draft')
                                ->required(),
                        ]),

                        Textarea::make('idea')
                            ->label('Descreva a ideia')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Resultado gerado')
                    ->schema([
                        TextInput::make('title')->label('Título'),
                        Textarea::make('caption')->label('Legenda')->rows(8)->columnSpanFull(),
                        Textarea::make('cta')->label('CTA')->rows(2),
                        Textarea::make('hashtags')->label('Hashtags')->rows(2),
                        TextInput::make('score')->label('Score')->numeric(),
                    ]),
            ]);
    }
}
