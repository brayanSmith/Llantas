<?php

namespace App\Filament\Resources\Compras\Schemas\Concerns;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Placeholder;
use Carbon\Carbon;
use App\Models\Producto;
use Dom\Text;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Radio;
use App\Services\VencimientoService;
use App\Services\ProximoAbonoService;
use App\Services\CompraCalculoService;

trait HasCompraSections
{
    // placeholders informativos (vencimiento / proximo abono)
    protected static function placeholders()
    {

        return [
            Placeholder::make('vencimiento_info')
                ->content(fn($get) => VencimientoService::mensaje($get('fecha_vencimiento')))
                ->extraAttributes(fn($get) => [
                    'class' => VencimientoService::estilo($get('fecha_vencimiento'))
                ])
                ->visible(fn($get) => $get('estado') === 'PENDIENTE' && !empty($get('fecha_vencimiento')))
                ->columnSpanFull(),
            Placeholder::make('proximo_abono')
                ->content(fn($get) => ProximoAbonoService::mensaje($get('abonos') ?? []))
                ->extraAttributes(fn($get) => [
                    'class' => ProximoAbonoService::estilo($get('abonos') ?? [])
                ])
                ->visible(fn($get) => !empty($get('abonos')) && ($get('total_a_pagar') ?? 0) > 0 && $get('estado') === 'FACTURADO' && $get('estado_pago') !== 'PAGADO')
                ->columnSpanFull(),
                ];
    }

    // sección datos generales
    protected static function sectionDatosGenerales(): array
    {
        return [
            Section::make('Datos de la compra')
                ->columns(4)
                ->columnSpan(1)
                ->schema([

                    /*TextInput::make('id')
                    ->columnSpan(1)
                    ->label('Id')
                    //->required()
                    ->unique(),*/
                    Radio::make('item_compra')
                    ->label('Tipo de Ítem')
                    ->inline()
                    ->columnSpan(4)
                    ->required()
                    ->live()
                    ->default('PRODUCTO')
                    ->options([
                        'PRODUCTO' => 'Producto',
                        'GASTO' => 'Gasto',
                    ]),

                    Radio::make('categoria_compra')
                    ->label('Categoría de Producto')
                    ->inline()
                    ->columnSpan(4)
                    ->required(fn($get) => $get('item_compra') === 'PRODUCTO')
                    ->live()
                    ->visible(fn($get) => $get('item_compra') === 'PRODUCTO')
                    ->default(fn($get) => $get('item_compra') === 'PRODUCTO' ? 'PRODUCTO_TERMINADO' : null)
                    ->options([
                        'MATERIA_PRIMA' => 'Materia Prima',
                        'PRODUCTO_TERMINADO' => 'Producto Terminado',
                        'OTRO' => 'Otro',
                    ]),

                    TextInput::make('factura')
                    ->columnSpan(1)
                    ->label('Factura')
                    ->required()
                    ->unique(),

                    Select::make('proveedor_id')
                        ->label('Proveedor')
                        ->relationship('proveedor', 'razon_social_proveedor')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->reactive()
                        ->columnSpan(3),

                    DatePicker::make('fecha')
                        ->label('Fecha de Recibido')
                        ->required()
                        ->columnSpan(2)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if ($state) {
                                try {
                                    $dias = (int) ($get('dias_plazo_vencimiento') ?? 0);
                                    $fechaVenc = VencimientoService::calcularFechaVencimiento($state, $dias);
                                    $set('fecha_vencimiento', $fechaVenc);
                                } catch (\Throwable $e) {
                                    // no hacer nada si hay error de parseo
                                }
                            } else {
                                $set('fecha_vencimiento', null);
                            }
                        }),

                    TextInput::make('dias_plazo_vencimiento')
                        ->label('Días Plazo Vencimiento')
                        ->default(30)
                        ->numeric()
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if ($state) {
                                try {
                                    $fechaVenc = VencimientoService::calcularFechaVencimiento($get('fecha'), $get('dias_plazo_vencimiento'));
                                    $set('fecha_vencimiento', $fechaVenc);
                                } catch (\Throwable $e) {
                                    // no hacer nada si hay error de parseo
                                }
                            } else {
                                $set('fecha_vencimiento', null);
                            }
                        })
                        ->minValue(0)
                        ->maxValue(365)
                        ->step(1)
                        //->helperText('Número de días para calcular la fecha de vencimiento a partir de la fecha de facturación.')
                        ->columnSpan(2),

                    Select::make('metodo_pago')
                    ->options([
                        'CREDITO' => 'Crédito',
                        'CONTADO' => 'Contado'
                        ])
                    ->default('CREDITO')
                    ->required()
                    ->columnSpan(2),

                    DatePicker::make('fecha_vencimiento')
                    ->label('Fecha de Vencimiento')
                    ->default(null)
                    ->columnSpan(2)
                    ->readOnly(),

                    ToggleButtons::make('estado')
                    ->options([
                        'PENDIENTE' => 'Pendiente',
                        'FACTURADO' => 'Facturado',
                        'ANULADO'   => 'Anulado',
                    ])
                    //->visible(fn($get) => ($get('estado') ?? '') !== 'PENDIENTE')
                    ->default('PENDIENTE')
                    ->required()
                    ->columnSpan(2)
                    ->grouped()
                    ->reactive(),

                    Select::make('tipo_compra')->options([
                        'REMISIONADA' => 'Remisionada',
                        'ELECTRONICA' => 'Electrónica',
                    ])->required()->columnSpan(2),

                    Select::make('bodega_id')
                    ->relationship('bodega', 'nombre_bodega')
                    ->required()
                    ->columnSpan(2),
                ]),
        ];
    }

    // sección resumen
    protected static function sectionResumen(): array
    {
        return [
            Section::make('Resumen')
                ->schema([
                    TextInput::make('subtotal')
                    ->currencyMask(".", ",", 2)
                    ->prefix('$')
                    ->readOnly()
                    ->numeric(),
                    TextInput::make('abono')
                    ->prefix('$')
                    ->currencyMask(".", ",", 2)
                    ->readOnly()
                    ->dehydrated()
                    ->numeric(),
                    TextInput::make('descuento')
                    ->prefix('$')
                    ->currencyMask(".", ",", 2)
                    ->numeric()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $set, callable $get) {
                            $data = CompraCalculoService::calcular(
                                $get('detallesCompra') ?? [],
                                $get('abonos') ?? [],
                                $get('descuento') ?? 0,
                            );
                            $set('subtotal', $data['subtotal']);
                            $set('abono', $data['abono']);
                            $set('total_a_pagar', $data['total_a_pagar']);
                            $set('saldo_pendiente', $data['saldo_pendiente']);
                        }
                    ),
                    TextInput::make('total_a_pagar')
                    ->label('Total a pagar')
                    ->prefix('$')
                    ->currencyMask(".", ",", 2)
                    ->readOnly()
                    ->numeric(),
                    TextInput::make('saldo_pendiente')
                    ->label('Saldo pendiente')
                    ->prefix('$')
                    ->currencyMask(".", ",", 2)
                    ->readOnly()
                    ->numeric(),
                ])->columnSpan(1),
        ];
    }

    //seccion primer comentario y segundo comentario
    protected static function sectionComentarios(): array
    {
        return [
            Section::make('Observaciones')
                ->columns(1)
                ->schema([
                    Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->rows(2)
                    ->columnSpanFull(),
                ])
                ->columnSpanFull()
                ->collapsed(true)
                ->collapsible(),
        ];
    }

    // sección detalles
    protected static function sectionDetalles(): array
    {
        return [
            Section::make('Detalles de la compra')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('detallesCompra')
                        ->relationship('detallesCompra')
                        ->label(function ($get) {
                            $detalles = $get('detallesCompra') ?? [];
                            $total = collect($detalles)->sum(callback: fn($detalle) => (float) ($detalle['subtotal'] ?? 0));
                            return 'Productos añadidos (Total: $' . number_format($total, 2, ',', '.') . ')';
                        })
                        ->table([
                            TableColumn::make('Item')->markAsRequired()->width('30%'),
                            TableColumn::make('Descripción')->width('20%'),
                            TableColumn::make('Cantidad')->markAsRequired()->width('5%'),
                            TableColumn::make('Pu')->markAsRequired()->width('10%'),
                            TableColumn::make('IVA')->markAsRequired()->width('10%'),
                            TableColumn::make('Pu + IVA')->markAsRequired()->width('10%'),
                            TableColumn::make('Subtotal')->markAsRequired()->width('10%'),
                        ])
                        ->compact()
                        ->schema([

                            Select::make('item_id')
                                ->label('Item')
                                ->searchable()
                                ->required()
                                ->preload()
                                // Opciones: si el item_compra es GASTO, me va a traer la de la tabla de gastos y si es PRODUCTO me va a traer la de productos
                                ->options(function ($get) {
                                    $tipoItem = $get('../../item_compra');
                                    if ($tipoItem === 'GASTO') {
                                        return \App\Models\Puc::orderBy('concatenar_subcuenta_concepto')->pluck('concatenar_subcuenta_concepto', 'id')->toArray();
                                    } else {
                                        $categoriaCompra = $get('../../categoria_compra');
                                        return \App\Models\Producto::when($categoriaCompra, function ($query, $categoria) {
                                                return $query->where('categoria_producto', $categoria);
                                            })
                                            ->orderBy('codigo_producto')
                                            ->pluck('concatenar_codigo_nombre', 'id')
                                            ->toArray();
                                    }
                                })

                                ->reactive()
                                ->columnSpan(2),

                            TextInput::make('descripcion_item')
                                ->label('Descripción')
                                ->columnSpan(2),

                            TextInput::make('cantidad')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function($state, $set, $get) {
                                    $resultado = CompraCalculoService::calcularDetalles($get());
                                    $set('subtotal', $resultado['subtotal']);
                                    $set('precio_con_iva', $resultado['precio_con_iva']);
                                })
                                ->columnSpan(1),

                            TextInput::make('precio_unitario')
                                ->prefix('$')
                                ->currencyMask(".", ",", 2)
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->readOnly(false)
                                ->afterStateUpdated(function($state, $set, $get) {
                                    $resultado = CompraCalculoService::calcularDetalles($get());
                                    $set('subtotal', $resultado['subtotal']);
                                    $set('precio_con_iva', $resultado['precio_con_iva']);
                                })
                                ->columnSpan(1),

                                TextInput::make('iva')
                                    ->prefix('%')
                                    //->percentageMask(",", ".")
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function($state, $set, $get) {
                                    $resultado = CompraCalculoService::calcularDetalles($get());
                                    $set('subtotal', $resultado['subtotal']);
                                    $set('precio_con_iva', $resultado['precio_con_iva']);
                                })
                                    ->columnSpan(1),
                            TextInput::make('precio_con_iva')
                                ->prefix('$')
                                ->currencyMask(".", ",", 2)
                                ->numeric()
                                ->readonly()
                                ->dehydrated(false)
                                ->columnSpan(1),

                            TextInput::make('subtotal')
                                ->prefix('$')
                                ->currencyMask(".", ",", 2)
                                ->numeric()
                                ->readonly()
                                ->dehydrated(false)
                                ->columnSpan(1),
                        ])
                        ->live()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $data = CompraCalculoService::calcular(
                                $get('detallesCompra') ?? [],
                                $get('abonos') ?? [],
                                $get('descuento') ?? 0,
                            );
                            $set('subtotal', $data['subtotal']);
                            $set('abono', $data['abono']);
                            $set('total_a_pagar', $data['total_a_pagar']);
                            $set('saldo_pendiente', $data['saldo_pendiente']);
                        }
                    )


                        ->addActionLabel('Añadir Producto')
                        ->deleteAction(fn(\Filament\Actions\Action $action) => $action->after(function ($record, $set, $get) {
                            $data = CompraCalculoService::calcular(
                                $get('detallesCompra') ?? [],
                                $get('abonos') ?? [],
                                $get('descuento') ?? 0,
                            );
                            $set('subtotal', $data['subtotal']);
                            $set('abono', $data['abono']);
                            $set('total_a_pagar', $data['total_a_pagar']);
                            $set('saldo_pendiente', $data['saldo_pendiente']);
                        })),
                ])->disabled(fn($get) => $get('estado') !== 'PENDIENTE'),
        ];
    }

    // sección abonos (visible solo facturado)
    protected static function sectionAbonos(): array
    {
        return [
            Section::make('Abonos Compra')
                ->columnSpanFull()
                ->visible(fn($get) => $get('estado') === 'FACTURADO')
                ->schema([
                    Repeater::make('abonos')
                        ->relationship('abonoCompra')
                        ->label(function ($get) {
                            $abonos = $get('abonos') ?? [];
                            $total = collect($abonos)->sum(fn($abono) => (float) ($abono['monto_abono_compra'] ?? 0));
                            return 'Abonos realizados (Total: $' . number_format($total, 2, ',', '.') . ')';
                        })
                        ->schema([
                            Section::make('Datos del abono')
                            ->schema([
                                DateTimePicker::make('fecha_abono_compra')
                                ->label('Fecha')
                                ->required()
                                ->default(now())
                                ->columnSpan(1),

                                TextInput::make('monto_abono_compra')
                                ->label('Monto')
                                ->prefix('$')
                                ->inputMode('decimal')
                                ->currencyMask(".", ",", 2)
                                ->required()
                                ->stripCharacters('.')
                                ->live(onBlur: true)
                                ->numeric()
                                ->columnSpan(1),

                                Select::make('forma_pago_abono_compra')
                                ->label('Forma de pago')
                                ->relationship(
                                    name:'formaPagoAbonoCompra',
                                    titleAttribute: 'concatenar_subcuenta_concepto',
                                    modifyQueryUsing: fn ($query) => $query->where('tipo', 1)

                                    )
                                ->searchable()
                                ->required()
                                ->preload()
                                ->columnSpan(1),

                                Textarea::make('descripcion_abono_compra')
                                ->label('Descripción')
                                ->default(null)
                                ->columnSpan(2),

                                Select::make('user_id')
                                ->label('Usuario que registra')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->default(auth()->id())
                                ->columnSpan(1),
                            ])->columns(3)->columnSpan(2),

                            Section::make('Soporte')->schema([
                                FileUpload::make('imagen_abono_compra')->label('Comprobante o evidencia')->directory('abonos')->image()->imagePreviewHeight('200')->columnSpanFull(),
                            ])->columnSpan(1),
                        ])
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $data = CompraCalculoService::calcular(
                                $get('detallesCompra') ?? [],
                                $get('abonos') ?? [],
                                $get('descuento') ?? 0,
                            );
                            $set('subtotal', $data['subtotal']);
                            $set('abono', $data['abono']);
                            $set('total_a_pagar', $data['total_a_pagar']);
                            $set('saldo_pendiente', $data['saldo_pendiente']);
                        }
                    )
                        ->addActionLabel('Añadir Abono')
                        ->columns(3)
                        ->columnSpan(4)
                        ->disabled(fn($get) => $get('estado') === 'ANULADO')
                        ->hidden(fn($get) => $get('estado') === 'ANULADO'),

                ])

        ];
    }
}
