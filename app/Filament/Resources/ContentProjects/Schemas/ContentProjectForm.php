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
        $category = self::selectedCategory();
        $content = self::categoryContent($category);

        return $schema
            ->components([
                Wizard::make([
                    Step::make('Cliente')
                        ->icon('heroicon-o-user-group')
                        ->description('Para quem vamos criar este conteúdo?')
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
                                ->label('Identidade visual')
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

                    Step::make($content['step'])
                        ->icon($content['icon'])
                        ->description($content['description'])
                        ->schema([
                            Textarea::make('idea')
                                ->label(function ($get) use ($content): string {
                                    $clientName = Client::query()->whereKey($get('client_id'))->value('name');

                                    if (! $clientName) {
                                        return $content['question'];
                                    }

                                    return "Vamos criar este conteúdo para {$clientName}. {$content['question']}";
                                })
                                ->placeholder($content['placeholder'])
                                ->helperText('Escreva do seu jeito. Inclua preço, data, oferta ou outra informação importante, quando houver. A VIA organiza o restante.')
                                ->rows(7)
                                ->required()
                                ->columnSpanFull(),

                            Hidden::make('objective')->default($content['objective']),
                            Hidden::make('channel')->default('instagram'),
                            Hidden::make('format')->default('post_portrait'),
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

    private static function selectedCategory(): string
    {
        $category = (string) request()->query('category', 'outro');

        return array_key_exists($category, self::categories()) ? $category : 'outro';
    }

    private static function categoryContent(string $category): array
    {
        return self::categories()[$category];
    }

    private static function categories(): array
    {
        return [
            'promocao' => [
                'step' => 'Sua promoção',
                'icon' => 'heroicon-o-tag',
                'description' => 'Vamos transformar sua oferta em uma publicação atrativa.',
                'question' => 'Conte qual produto ou serviço está em promoção.',
                'placeholder' => 'Ex.: Pizza grande por R$ 49,90 até domingo.',
                'objective' => 'sales',
            ],
            'produto_servico' => [
                'step' => 'Produto ou serviço',
                'icon' => 'heroicon-o-shopping-bag',
                'description' => 'Apresente o que sua empresa oferece de forma clara.',
                'question' => 'Qual produto ou serviço você quer apresentar?',
                'placeholder' => 'Ex.: Limpeza de pele com avaliação personalizada.',
                'objective' => 'sales',
            ],
            'evento' => [
                'step' => 'Seu evento',
                'icon' => 'heroicon-o-calendar-days',
                'description' => 'Informe os dados principais e a VIA prepara a divulgação.',
                'question' => 'Conte qual evento você quer divulgar.',
                'placeholder' => 'Ex.: Feira de negócios, sábado, às 10h, no centro da cidade.',
                'objective' => 'event',
            ],
            'dica' => [
                'step' => 'Sua dica',
                'icon' => 'heroicon-o-light-bulb',
                'description' => 'Compartilhe uma orientação útil com seu público.',
                'question' => 'Qual dica você quer compartilhar?',
                'placeholder' => 'Ex.: Três cuidados para conservar melhor seus alimentos.',
                'objective' => 'education',
            ],
            'novidade' => [
                'step' => 'Sua novidade',
                'icon' => 'heroicon-o-newspaper',
                'description' => 'Conte o que mudou ou chegou de novo na sua empresa.',
                'question' => 'Qual novidade você quer contar?',
                'placeholder' => 'Ex.: Agora também atendemos aos sábados.',
                'objective' => 'engagement',
            ],
            'data_especial' => [
                'step' => 'Data especial',
                'icon' => 'heroicon-o-heart',
                'description' => 'Crie uma homenagem adequada à ocasião.',
                'question' => 'Qual data ou ocasião você quer homenagear?',
                'placeholder' => 'Ex.: Dia das Mães, com uma mensagem carinhosa para nossas clientes.',
                'objective' => 'community',
            ],
            'comunicado' => [
                'step' => 'Seu comunicado',
                'icon' => 'heroicon-o-megaphone',
                'description' => 'Transmita uma informação importante com clareza.',
                'question' => 'O que você precisa comunicar?',
                'placeholder' => 'Ex.: Não abriremos na segunda-feira por causa do feriado.',
                'objective' => 'institutional',
            ],
            'outro' => [
                'step' => 'Sua ideia',
                'icon' => 'heroicon-o-sparkles',
                'description' => 'Conte sua ideia. A VIA ajuda a transformar em conteúdo.',
                'question' => 'Que conteúdo você quer criar?',
                'placeholder' => 'Escreva sua ideia com as informações que considerar importantes.',
                'objective' => 'engagement',
            ],
        ];
    }
}
