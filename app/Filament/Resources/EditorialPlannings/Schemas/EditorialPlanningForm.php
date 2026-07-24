<?php

namespace App\Filament\Resources\EditorialPlannings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditorialPlanningForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Planejamento editorial')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('client_id')
                                ->label('Cliente')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            TextInput::make('theme')
                                ->label('Tema')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('objective')
                                ->label('Objetivo')
                                ->maxLength(255),

                            DatePicker::make('planned_for')
                                ->label('Data planejada')
                                ->required(),
                        ]),
                    ]),

                Section::make('Publicação')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('channel')
                                ->label('Canal')
                                ->options([
                                    'instagram' => 'Instagram',
                                    'facebook' => 'Facebook',
                                    'linkedin' => 'LinkedIn',
                                    'threads' => 'Threads',
                                ])
                                ->default('instagram')
                                ->required(),

                            Select::make('format')
                                ->label('Formato')
                                ->options([
                                    'post' => 'Post',
                                    'reel' => 'Reel',
                                    'story' => 'Story',
                                    'carousel' => 'Carrossel',
                                    'video' => 'Vídeo',
                                ])
                                ->default('post')
                                ->required(),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'planned' => 'Planejado',
                                    'writing' => 'Em produção',
                                    'caption_ready' => 'Legenda pronta',
                                    'caption_approved' => 'Legenda aprovada',
                                    'image_ready' => 'Imagem pronta',
                                    'image_approved' => 'Imagem aprovada',
                                    'scheduled' => 'Agendado',
                                    'published' => 'Publicado',
                                    'archived' => 'Arquivado',
                                ])
                                ->default('planned')
                                ->required(),

                            Select::make('priority')
                                ->label('Prioridade')
                                ->options([
                                    1 => 'Urgente',
                                    2 => 'Alta',
                                    3 => 'Normal',
                                    4 => 'Baixa',
                                ])
                                ->default(3)
                                ->required(),
                        ]),
                    ]),

                Section::make('Observações')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notas internas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
