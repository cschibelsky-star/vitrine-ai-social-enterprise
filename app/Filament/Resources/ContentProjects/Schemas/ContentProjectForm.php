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
                Section::make('1. Cliente e Brand Kit')
                    ->description('Escolha para quem este conteúdo será criado. O Brand Kit orienta tom de voz, público e estilo da IA.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('client_id')
                                ->label('Cliente')
                                ->options(fn () => Client::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('brand_id')
                                ->label('Brand Kit')
                                ->options(fn () => Brand::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                    ]),

                Section::make('2. Estratégia do conteúdo')
                    ->description('Defina o objetivo, rede social e formato. Esta etapa funciona como o briefing do BestContent.')
                    ->schema([
                        Grid::make(3)->schema([
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

                            Select::make('channel')
                                ->label('Canal')
                                ->options([
                                    'instagram' => 'Instagram',
                                    'facebook' => 'Facebook',
                                    'linkedin' => 'LinkedIn',
                                    'tiktok' => 'TikTok',
                                    'whatsapp' => 'WhatsApp',
                                ])
                                ->default('instagram')
                                ->required(),

                            Select::make('format')
                                ->label('Formato')
                                ->options([
                                    'post_portrait' => 'Post Portrait',
                                    'carousel_portrait' => 'Carrossel Portrait',
                                    'stories' => 'Stories',
                                    'reels' => 'Reels',
                                    'facebook_post' => 'Facebook Post',
                                    'linkedin_post' => 'LinkedIn Post',
                                ])
                                ->default('post_portrait')
                                ->required(),
                        ]),
                    ]),

                Section::make('3. Ideia principal')
                    ->description('Escreva em linguagem simples. Ao criar o projeto, a IA gera título, legenda, CTA, hashtags, score e slides.')
                    ->schema([
                        Textarea::make('idea')
                            ->label('O que você deseja comunicar?')
                            ->placeholder('Ex.: Criar uma campanha para divulgar uma promoção de inverno no Instagram.')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('4. Resultado gerado pela IA')
                    ->description('Depois que o conteúdo for criado, estes campos serão preenchidos automaticamente e poderão ser editados.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')->label('Título gerado'),
                            TextInput::make('score')->label('Score IA')->numeric(),
                        ]),

                        Textarea::make('caption')->label('Legenda final')->rows(8)->columnSpanFull(),
                        Textarea::make('cta')->label('CTA')->rows(2),
                        Textarea::make('hashtags')->label('Hashtags')->rows(2),

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
            ]);
    }
}
