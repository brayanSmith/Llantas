<?php

namespace App\Filament\Resources\PedidoCotizacions\Schemas;

use App\Models\Producto;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use App\Services\Pedido\PedidoCalculoService;

class PedidoCotizacionDetail
{
    public static function sectionDetalles(): array
    {
        return [
            Section::make('Detalles del pedido')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('detalles')
                        ->relationship('detalles')
                        ->compact()
                        ->schema([
                            Section::make()
                                ->columns(7)
                                ->extraAttributes(function ($get) {
                                    return array_merge(
                                        \App\Filament\Forms\Components\AlpinePrecioCantidad::alpineBindings($get),
                                        ['class' => 'pos-row-container']
                                    );
                                })
                                ->schema([
                                    Select::make('producto_id')
                                        ->label('Producto')
                                        ->relationship('producto', 'concatenar_codigo_nombre')
                                        ->searchable()
                                        ->preload()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function($state, $set, $get) {
                                            $tipoPrecio = $get('../../tipo_precio') ?? 'FERRETERO';
                                            $productoModel = Producto::find($state);

                                            $precio = PedidoCalculoService::obtenerValorUnitario($productoModel, $tipoPrecio);
                                            $set('precio_unitario', $precio);

                                            $datos = PedidoCalculoService::obtenerDatosProducto($productoModel);
                                            $set('iva', $datos['iva'] ?? 0);
                                        })
                                        ->columnSpan(2),

                                    TextInput::make('cantidad')
                                        ->numeric()
                                        ->default(1)
                                        ->columnSpan(1)
                                        // Usamos @input para actualizar la variable de Alpine manualmente si el model falla
                                        ->extraInputAttributes([
                                            'x-model.number' => 'cantidad',
                                            'x-on:input' => 'cantidad = parseFloat($event.target.value)'
                                        ]),

                                    TextInput::make('precio_unitario')
                                        ->numeric()
                                        ->columnSpan(1)
                                        ->extraInputAttributes([
                                            'x-model.number' => 'precio',
                                            'x-on:input' => 'precio = parseFloat($event.target.value)'
                                        ]),

                                    Checkbox::make('aplicar_iva')
                                        ->label('IVA')
                                        ->default(true)
                                        ->columnSpan(1)
                                        // Forma ultra-segura de cambiar el estado en Alpine
                                        ->extraAttributes([
                                            'x-on:click' => 'conIva = !conIva',
                                        ]),

                                    TextInput::make('precio_con_iva')
                                        ->disabled()
                                        ->dehydrated(true)
                                        ->columnSpan(1)
                                        ->extraInputAttributes([
                                            ':value' => '(conIva ? (precio * (1 + ivaFactor)) : precio).toFixed(2)',
                                        ]),

                                    TextInput::make('subtotal')
                                        ->disabled()
                                        ->dehydrated(true)
                                        ->columnSpan(1)
                                        ->extraAttributes(['class' => 'fila-subtotal'])
                                        ->extraInputAttributes([
                                            ':value' => '(conIva ? (precio * (1 + ivaFactor) * cantidad) : (precio * cantidad)).toFixed(2)',
                                        ]),
                                ])
                        ])
                ])
        ];
    }
}
