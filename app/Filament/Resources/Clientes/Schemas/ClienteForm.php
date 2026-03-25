<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Datos Generales')
                            ->columns(2)
                            ->schema([

                                Select::make('tipo_documento')
                                    ->options([
                                        'CC' => 'CC',
                                        'NIT' => 'NIT',
                                        'CE' => 'CE',
                                    ])
                                    ->required(),
                                TextInput::make('numero_documento')
                                    ->required(),
                                TextInput::make('razon_social')
                                    ->required(),
                                TextInput::make('direccion')
                                    ->required(),
                                TextInput::make('telefono')
                                    ->tel()
                                    ->required(),
                                TextInput::make('ciudad')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required(),

                                Select::make('novedad')
                                    ->options([
                                        'Nuevo' => 'Nuevo',
                                        'Regular' => 'Regular',
                                        'Moroso' => 'Moroso',
                                    ])
                                    ->default(null),

                                FileUpload::make('rut_imagen')
                                    ->label('Imagen RUT')
                                    ->directory('clientes/rut')
                                    ->disk('public')
                                    ->image()
                                    ->maxSize(1024) // 1MB
                                    ->nullable(),
                                ToggleButtons::make('retenedor_fuente')
                                    ->options([
                                        'SI' => 'SI',
                                        'NO' => 'NO',
                                    ])
                                    ->default('NO')
                                    ->grouped()
                                    ->required(),

                                Toggle::make('activo')
                                    ->required()
                                    ->default(true),

                            ]),
                        ]),

            ]);

    }
}
