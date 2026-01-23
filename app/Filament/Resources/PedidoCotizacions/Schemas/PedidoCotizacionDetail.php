<?php

namespace App\Filament\Resources\PedidoCotizacions\Schemas;

use App\Models\Producto;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use App\Services\Pedido\PedidoCalculoService;
use Filament\Forms\Components\Repeater\TableColumn;

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
                        ->label(function ($get) {
                            $detalles = $get('detalles') ?? [];
                            $total = collect($detalles)->sum(callback: fn($detalle) => (float) ($detalle['subtotal'] ?? 0));
                            return 'Productos añadidos (Total: $' . number_format($total, 0, ',', '.') . ')';
                        })
                        ->table([
                            //TableColumn::make('Código')->width('50px'),
                            TableColumn::make('Producto')->markAsRequired()->width('250px'),
                            TableColumn::make('Cantidad')->markAsRequired()->width('20px'),
                            TableColumn::make('Precio Unitario')->markAsRequired()->width('100px'),
                            TableColumn::make('IVA')->markAsRequired()->width('5px'),
                            TableColumn::make('Precio con IVA')->markAsRequired()->width('100px'),
                            TableColumn::make('Subtotal')->markAsRequired()->width('100px'),
                        ])
                        ->compact()
                        ->schema([

                            Select::make('producto_id')
                                ->label('Producto')
                                ->relationship('producto', 'concatenar_codigo_nombre')
                                ->searchable()
                                ->required()
                                ->preload()
                                ->reactive()
                                ->afterStateUpdated(function($state, $set, $get) {
                                    // Obtener el tipo de precio del pedido
                                    $tipoPrecio = $get('../../tipo_precio') ?? 'FERRETERO';

                                    $precio = PedidoCalculoService::obtenerValorUnitario(Producto::find($state), $tipoPrecio);
                                    $set('precio_unitario', $precio);

                                    $producto = PedidoCalculoService::obtenerDatosProducto(Producto::find($state));
                                    $set('iva', $producto['iva'] ?? 0);
                                })
                                ->columnSpan(2),

                            TextInput::make('cantidad')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->columnSpan(1)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function($state, $set, $get) {
                                    $resultado = PedidoCalculoService::calcularDetalles($get());
                                    $set('subtotal', $resultado['subtotal']);
                                    $set('precio_con_iva', $resultado['precio_con_iva']);
                                }),

                            TextInput::make('precio_unitario')
                                ->prefix('$')
                                ->currencyMask(".", ",", 0)
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                // ahora editable por el usuario; si el usuario cambia este valor
                                // recalculamos subtotal sin sobreescribir el precio
                                ->readOnly(false)
                                ->afterStateUpdated(function($state, $set, $get) {
                                    $resultado = PedidoCalculoService::calcularDetalles($get());
                                    $set('subtotal', $resultado['subtotal']);
                                    $set('precio_con_iva', $resultado['precio_con_iva']);
                                })
                                ->columnSpan(1),
                            Checkbox::make('aplicar_iva')
                                ->label('Aplicar IVA')
                                ->default(true)
                                ->reactive()
                                ->afterStateUpdated(function($state, $set, $get) {
                                    $resultado = PedidoCalculoService::calcularDetalles($get());
                                    $set('subtotal', $resultado['subtotal']);
                                    $set('precio_con_iva', $resultado['precio_con_iva']);
                                })
                                ->columnSpan(1),

                            TextInput::make('precio_con_iva')
                                ->prefix('$')
                                ->currencyMask(".", ",", 0)
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->columnSpan(1),

                            TextInput::make('subtotal')
                                ->prefix('$')
                                ->currencyMask(".", ",", 0)
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->columnSpan(1),
                        ])
                        ->live()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $data = PedidoCalculoService::calcularTotalesPedido(
                                $get('detalles') ?? [],
                                $get('abonos') ?? [],
                                $get('descuento') ?? 0,
                                $get('flete') ?? 0
                            );
                            $set('subtotal', $data['subtotal']);
                            $set('abono', $data['abono']);
                            $set('total_a_pagar', $data['total_a_pagar']);
                            $set('saldo_pendiente', $data['saldo_pendiente']);
                        })

                        ->addActionLabel('Añadir Producto')
                        ->deletable(true)


                ])->disabled(fn($get) => $get('estado') !== 'PENDIENTE'),
        ];
    }

}
