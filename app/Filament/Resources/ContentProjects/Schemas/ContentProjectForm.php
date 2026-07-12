<?php

namespace App\Filament\Resources\ContentProjects\Schemas;

use App\Models\Brand;
use App\Models\Client;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class ContentProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Cliente')
                        ->icon('heroicon-o-user-group')
                        ->description('Escolha para quem o conteúdo será criado.')
                        ->schema([
                            Select::make('client_id')
                                ->label('Cliente')
                                ->options(fn () => Client::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('brand_id', null))
                                ->required(),

                            Select::make('brand_id')
                                ->label('Brand Kit')
                                ->options(function ($get) {
                                    $clientId = $get('client_id');

                                    if (! $clientId) {
                                        return [];
                                    }

                                    return Brand::query()
                                        ->where('client_id', $clientId)
                                        ->where('status', 'active')
                                        ->orderBy('name')
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->disabled(fn ($get) => blank($get('client_id'))),
                        ]),

                    Step::make('Formato')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->description('Defina rede social e tipo de conteúdo.')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('channel')
                                    ->label('Rede social')
                                    ->options([
                                        'instagram' => 'Instagram',
                                        'facebook' => 'Facebook',
                                        'linkedin' => 'LinkedIn',
                                        'tiktok' => 'TikTok',
                                        'threads' => 'Threads',
                                        'whatsapp' => 'WhatsApp',
                                    ])
                                    ->default('instagram')
                                    ->required(),

                                Select::make('format')
                                    ->label('O que deseja criar?')
                                    ->options([
                                        'post_portrait' => 'Post para feed',
                                        'carousel_portrait' => 'Carrossel',
                                        'stories' => 'Story',
                                        'reels' => 'Reels',
                                        'facebook_post' => 'Post para Facebook',
                                        'linkedin_post' => 'Post para LinkedIn',
                                    ])
                                    ->default('post_portrait')
                                    ->required(),
                            ]),
                        ]),

                    Step::make('Objetivo')
                        ->icon('heroicon-o-bullseye')
                        ->description('Informe o resultado que o conteúdo deve gerar.')
                        ->schema([
                            Select::make('objective')
                                ->label('Objetivo principal')
                                ->options([
                                    'sales' => 'Vender',
                                    'engagement' => 'Gerar engajamento',
                                    'authority' => 'Construir autoridade',
                                    'education' => 'Educar o público',
                                    'community' => 'Fortalecer comunidade',
                                    'institutional' => 'Conteúdo institucional',
                                    'event' => 'Divulgar evento',
                                    'launch' => 'Divulgar lançamento',
                                ])
                                ->default('engagement')
                                ->required(),
                        ]),

                    Step::make('Tema')
                        ->icon('heroicon-o-sparkles')
                        ->description('Escreva apenas a ideia. O Studio fará o restante.')
                        ->schema([
                            Textarea::make('idea')
                                ->label('Sobre o que devemos criar?')
                                ->placeholder('Ex.: Campanha de inverno com desconto especial durante esta semana.')
                                ->helperText('Inclua datas, oferta, produto ou informação obrigatória quando necessário.')
                                ->rows(7)
                                ->required()
                                ->columnSpanFull(),

                            Hidden::make('status')->default('draft'),
                            Hidden::make('generation_method')->default('from_scratch'),
                        ]),
                ])
                    ->columnSpanFull()
                    ->persistStepInQueryString('etapa'),

                Section::make('Conteúdo gerado')
                    ->description('Edite o material após a geração automática do Studio.')
                    ->visibleOn('edit')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')->label('Título'),
                            TextInput::make('score')->label('Score IA')->numeric(),
                        ]),

                        Textarea::make('caption')->label('Legenda')->rows(10)->columnSpanFull(),

                        Grid::make(2)->schema([
                            Textarea::make('cta')->label('Chamada para ação')->rows(3),
                            Textarea::make('hashtags')->label('Hashtags')->rows(3),
                        ]),

                        Select::make('status')
                            ->label('Status editorial')
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
            ]);
    }
}
