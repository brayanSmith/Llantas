<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Placeholder;

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
                                TextInput::make('representante_legal')
                                    ->required(),
                                Select::make('ruta_id')
                                    ->relationship('ruta', 'ruta')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('novedad')
                                    ->options([
                                        'Nuevo' => 'Nuevo',
                                        'Regular' => 'Regular',
                                        'Moroso' => 'Moroso',
                                    ])
                                    ->default(null),
                                Select::make('comercial_id')
                                    ->label('Comercial')
                                    ->relationship('comercial', 'name', fn ($query) => $query->where('role', 'COMERCIAL'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                ToggleButtons::make('tipo_cliente')
                                    ->label('Tipo Cliente')
                                    ->options([
                                        'ELECTRONICO' => 'ELECTRONICO',
                                        'REMISIONADO' => 'REMISIONADO',
                                    ])
                                    ->required()
                                    ->default('ELECTRONICO')
                                    ->grouped(),

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

                        Tab::make('Pedidos')
                            ->schema([
                                Repeater::make('pedidos')
                                    ->columnSpanFull()
                                    ->relationship('pedidos')
                                    ->deletable(false)
                                    ->addable(false)
                                    ->table([
                                        TableColumn::make('Pedido')->width('100px'),
                                        TableColumn::make('Vence El')->width('100px'),
                                        TableColumn::make('Saldo')->width('100px'),
                                        TableColumn::make('Estado')->width('100px'),
                                        TableColumn::make('Abonos')->width('100px'),
                                        TableColumn::make('Total')->width('100px'),
                                    ])
                                    ->compact()
                                    ->schema([
                                        TextInput::make('codigo')
                                            ->label('Pedido')
                                            ->disabled(),
                                        TextInput::make('fecha_vencimiento')
                                            ->label('Vence El')
                                            ->disabled(),
                                        TextInput::make('saldo_pendiente')
                                            ->label('Saldo')
                                            ->formatStateUsing(fn($state) => number_format($state, 0))
                                            ->disabled(),
                                        \Filament\Forms\Components\Placeholder::make('estado_pago_display')
                                            ->label('Estado')
                                            ->content(function ($record) {
                                                return $record ? $record->getEstadoPagoFactura() : 'N/A';
                                            })
                                            ->extraAttributes(function ($record) {
                                                if (!$record) return ['class' => 'text-gray-500 font-bold text-sm'];
                                                
                                                $estado = $record->getEstadoPagoFactura();
                                                return match ($estado) {
                                                    'SALDADO' => ['class' => 'text-green-600 font-bold text-sm'],
                                                    'VENCIDO' => ['class' => 'text-red-600 font-bold text-sm'],
                                                    'AL_DIA' => ['class' => 'text-blue-600 font-bold text-sm'],
                                                    default => ['class' => 'text-gray-500 font-bold text-sm'],
                                                };
                                            }),
                                        TextInput::make('abono')
                                            ->label('Abonos')
                                            ->formatStateUsing(fn($state) => number_format($state, 0))
                                            ->disabled(),
                                        TextInput::make('total_a_pagar')
                                            ->label('Total')
                                            ->formatStateUsing(fn($state) => number_format($state, 0))
                                            ->disabled(),
                                    ]),
                            ]),
                    ])->vertical(),
            ]);

    }
}
