<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do cliente')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nome do cliente')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('segment')
                                ->label('Segmento')
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
                    ]),

                Section::make('Contato')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('contact_name')->label('Responsável'),
                            TextInput::make('contact_email')->label('E-mail')->email(),
                            TextInput::make('contact_phone')->label('Telefone/WhatsApp'),
                            TextInput::make('website')->label('Site')->url(),
                            TextInput::make('instagram')->label('Instagram'),
                            TextInput::make('facebook')->label('Facebook'),
                        ]),
                    ]),
            ]);
    }
}
