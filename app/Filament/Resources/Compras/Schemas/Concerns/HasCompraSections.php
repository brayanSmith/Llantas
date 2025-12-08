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


trait HasCompraSections
{
    // placeholders informativos (vencimiento / proximo abono)
    protected static function placeholders()
    {

        return [
            Placeholder::make('vencimiento_info')
                ->content(function ($get) {
                    $fechaVenc = $get('fecha_vencimiento');
                    if (empty($fechaVenc)) return '';
                    try {
                        $hoy = Carbon::today();
                        $venc = Carbon::parse($fechaVenc)->startOfDay();
                        $dias = $hoy->diffInDays($venc, false);
                        if ($dias > 0) return "Quedan {$dias} día" . ($dias === 1 ? '' : 's') . " para vencerse";
                        if ($dias === 0) return 'Vence hoy';
                        return "Vencido hace " . abs($dias) . " día" . (abs($dias) === 1 ? '' : 's');
                    } catch (\Throwable $e) {
                        return '';
                    }
                })
                ->extraAttributes(function ($get) {
                    $fechaVenc = $get('fecha_vencimiento');
                    if (empty($fechaVenc)) return ['class' => 'text-sm mb-2'];
                    try {
                        $hoy = Carbon::today();
                        $venc = Carbon::parse($fechaVenc)->startOfDay();
                        $dias = $hoy->diffInDays($venc, false);
                        if ($dias > 3) $class = 'text-sm bg-green-600 text-green-50 mb-2 p-2 rounded';
                        elseif ($dias >= 1) $class = 'text-sm bg-yellow-600 text-yellow-50 mb-2 p-2 rounded';
                        elseif ($dias === 0) $class = 'text-sm bg-yellow-600 text-yellow-50 mb-2 p-2 rounded';
                        else $class = 'text-sm bg-red-600 text-red-50 mb-2 p-2 rounded';
                        return ['class' => $class];
                    } catch (\Throwable $e) {
                        return ['class' => 'text-sm mb-2'];
                    }
                })
                ->visible(fn($get) => $get('estado') === 'PENDIENTE' && ! empty($get('fecha_vencimiento')))
                ->columnSpanFull(),

            Placeholder::make('proximo_abono')
                ->content(function ($get) {
                    $abonos = $get('abonos') ?? [];
                    if (empty($abonos)) return '';
                    try {
                        // posibles nombres de campo fecha en distintos formularios
                        $dateKeys = ['fecha', 'fecha_abono', 'fecha_abono_compra', 'fecha_abono_pedido'];
                        $dates = collect($abonos)
                            ->map(function ($a) use ($dateKeys) {
                                foreach ($dateKeys as $k) {
                                    if (! empty($a[$k])) {
                                        try { return Carbon::parse($a[$k]); } catch (\Throwable $e) { return null; }
                                    }
                                }
                                return null;
                            })
                            ->filter()
                            ->sort();
                        $last = $dates->last();
                        if (! $last) return '';
                        $proximo = $last->copy()->addDays(30);
                        $dias = (int) Carbon::today()->diffInDays($proximo, false);
                        $label = $proximo->format('d/m/Y');
                        if ($dias > 0) return "Próximo abono: {$label} (en {$dias} día" . ($dias === 1 ? '' : 's') . ")";
                        if ($dias === 0) return "Próximo abono: {$label} (hoy)";
                        $vencidos = abs($dias);
                        return "Próximo abono: {$label} (vencido hace {$vencidos} día" . ($vencidos === 1 ? '' : 's') . ")";
                    } catch (\Throwable $e) {
                        return '';
                    }
                })
                ->extraAttributes(function ($get) {
                    $abonos = $get('abonos') ?? [];
                    if (empty($abonos)) return ['class' => 'text-sm mb-2'];
                    try {
                        $dateKeys = ['fecha', 'fecha_abono', 'fecha_abono_compra', 'fecha_abono_pedido'];
                        $dates = collect($abonos)
                            ->map(function ($a) use ($dateKeys) {
                                foreach ($dateKeys as $k) {
                                    if (! empty($a[$k])) {
                                        try { return Carbon::parse($a[$k]); } catch (\Throwable $e) { return null; }
                                    }
                                }
                                return null;
                            })
                            ->filter()
                            ->sort();
                        $last = $dates->last();
                        if (! $last) return ['class' => 'text-sm mb-2'];
                        $proximo = $last->copy()->addDays(30);
                        $dias = (int) Carbon::today()->diffInDays($proximo, false);
                        if ($dias > 7) return ['class' => 'text-sm bg-green-600 text-green-50 mb-2 p-2 rounded'];
                        if ($dias >= 1) return ['class' => 'text-sm bg-yellow-600 text-yellow-50 mb-2 p-2 rounded'];
                        return ['class' => 'text-sm bg-red-600 text-red-50 mb-2 p-2 rounded'];
                    } catch (\Throwable $e) {
                        return ['class' => 'text-sm mb-2'];
                    }
                })
                ->visible(fn($get) => ! empty($get('abonos')) && ((float) ($get('total_a_pagar') ?? 0) > 0))
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

                    TextInput::make('factura')->columnSpan(1)->label('Factura')->required()->unique(),

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
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if ($state) {
                                try {
                                    $dias = (int) ($get('dias_plazo_vencimiento') ?? 0);
                                    $fechaCarbon = Carbon::parse($state);
                                    $nuevaFechaVenc = $fechaCarbon->copy()->addDays($dias);
                                    $set('fecha_vencimiento', $nuevaFechaVenc->toDateString());
                                } catch (\Throwable $e) {
                                    // no hacer nada si hay error de parseo
                                }
                            } else {
                                $set('fecha_vencimiento', null);
                            }
                        }),

                    TextInput::make('dias_plazo_vencimiento')->label('Días Plazo Vencimiento')->default(30)->numeric()->required()->reactive()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $fecha = $get('fecha');
                            if ($fecha && $state !== null) {
                                try {
                                    $fechaCarbon = Carbon::parse($fecha);
                                    $nuevaFechaVenc = $fechaCarbon->copy()->addDays((int) $state);
                                    $set('fecha_vencimiento', $nuevaFechaVenc->toDateString());
                                } catch (\Throwable $e) {
                                    // no hacer nada si hay error
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

                    Select::make('metodo_pago')->options(['CREDITO' => 'Crédito', 'CONTADO' => 'Contado'])->default('CREDITO')->required()->columnSpan(2),

                    DatePicker::make('fecha_vencimiento')->label('Fecha de Vencimiento')->default(null)->columnSpan(2)->readOnly(),

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
                    
                    Select::make('bodega_id')->relationship('bodega', 'nombre_bodega')->required()->columnSpan(2),
                ]),
        ];
    }

    // sección resumen
    protected static function sectionResumen(): array
    {
        return [
            Section::make('Resumen')
                ->schema([
                    TextInput::make('subtotal')->currencyMask(".", ",", 0)->prefix('$')->readOnly()->numeric(),
                    TextInput::make('abono')->prefix('$')->currencyMask(".", ",", 0)->readOnly()->numeric(),
                    TextInput::make('descuento')->prefix('$')->currencyMask(".", ",", 0)->numeric()->live(onBlur: true)->afterStateUpdated(fn($state, $set, $get) => self::recalcularAbonos($set, $get)),
                    TextInput::make('total_a_pagar')->label('Total a pagar')->prefix('$')->currencyMask(".", ",", 0)->readOnly()->numeric(),
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
                    Textarea::make('observaciones')->label('Observaciones')->rows(2)->columnSpanFull(),
                ])->columnSpanFull()->collapsed(true)->collapsible(),
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
                            return 'Productos añadidos (Total: $' . number_format($total, 0, ',', '.') . ')';
                        })
                        ->mutateRelationshipDataBeforeSaveUsing(function (array $data, $record = null): array {
                            $data['producto_id'] = isset($data['producto_id']) ? (int) $data['producto_id'] : null;
                            $data['item_id'] = isset($data['item_id']) ? (int) $data['item_id'] : null;
                            $data['descripcion'] = $data['descripcion'] ?? '';
                            $data['cantidad'] = isset($data['cantidad']) ? (float) $data['cantidad'] : 0;
                            $data['precio_unitario'] = isset($data['precio_unitario']) ? (float) $data['precio_unitario'] : 0;
                            $data['subtotal'] = $data['cantidad'] * $data['precio_unitario'] / 100 * (100 + ($data['iva'] ?? 0));
                            if (isset($data['_remove_temp'])) unset($data['_remove_temp']);
                            return $data;
                        })
                        ->table([
                            //TableColumn::make('Código')->width('50px'),
                            TableColumn::make('Item')->markAsRequired()->width('200px'),
                            TableColumn::make('Descripción')->width('200px'),
                            TableColumn::make('Cantidad')
                            ->markAsRequired()                            
                            ->width('100px'),
                            TableColumn::make('Precio Unitario')->markAsRequired()->width('100px'),
                            TableColumn::make('IVA')
                            ->markAsRequired()
                            ->width('100px'),
                            TableColumn::make('Subtotal')->markAsRequired()->width('100px'),
                            //TableColumn::make('Acciones')->width('10px'),
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
                                ->afterStateUpdated(fn($state, $set, $get) => self::recalcularFila($set, $get))
                                ->columnSpan(2), 
                                

                            TextInput::make('cantidad')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, $set, $get) => self::recalcularFila($set, $get))
                                ->columnSpan(1),

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
                                ->afterStateUpdated(fn($state, $set, $get) => self::recalcularFila($set, $get))
                                ->columnSpan(1),

                                TextInput::make('iva')
                                    ->prefix('%')
                                    //->percentageMask(",", ".")
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, $set, $get) => self::recalcularFila($set, $get))
                                    ->columnSpan(1),

                            TextInput::make('subtotal')
                                ->prefix('$')
                                ->currencyMask(".", ",", 0)
                                ->numeric()
                                ->readonly()
                                ->dehydrated(true)
                                ->columnSpan(1),
                        ])
                        ->addActionLabel('Añadir Producto')
                        ->deleteAction(fn(\Filament\Actions\Action $action) => $action->after(function ($record, $set, $get) {
                            self::recalcularTodo($set, $get);
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
                            return 'Abonos realizados (Total: $' . number_format($total, 0, ',', '.') . ')';
                        })
                        ->schema([
                            Section::make('Datos del abono')->schema([
                                DateTimePicker::make('fecha_abono_compra')->label('Fecha')->required()->default(now())->columnSpan(1),
                                TextInput::make('monto_abono_compra')->label('Monto')->prefix('$')->inputMode('decimal')->currencyMask(".", ",", 0)->required()->stripCharacters('.')->live(onBlur: true)->numeric()->columnSpan(1),
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
                                Textarea::make('descripcion_abono_compra')->label('Descripción')->default(null)->columnSpan(2),
                                
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
                        ->addActionLabel('Añadir Abono')
                        ->columns(3)
                        ->columnSpan(4)
                        ->disabled(fn($get) => $get('estado') === 'ANULADO')
                        ->hidden(fn($get) => $get('estado') === 'ANULADO'),
                ])
                ->afterStateUpdated(function ($set, $get) {
                    self::recalcularAbonos($set, $get);
                }),
        ];
    }

    // ---------- helpers: recalcular todo / fila / abonos ----------
    private static function recalcularTodo(callable $set, callable $get): void
    {
        // detectar el nombre real del repeater usado en este form
        $repeaterKeys = ['detalles', 'detallesCompra', 'detalles_compra'];
        $repeaterKey = null;
        foreach ($repeaterKeys as $k) {
            try {
                $maybe = $get($k);
            } catch (\Throwable $e) {
                $maybe = null;
            }
            if (!is_null($maybe)) {
                $repeaterKey = $k;
                break;
            }
        }

        $detalles = $repeaterKey ? ($get($repeaterKey) ?? []) : [];
        $subtotalGeneral = 0;

        foreach ($detalles as $index => $detalle) {
            if (empty($detalle['producto_id'])) continue;
            $producto = Producto::find($detalle['producto_id']);
            if (! $producto) continue;
            $precio = $detalle['precio_unitario'] ?? 0;
            $cantidad = $detalle['cantidad'] ?? 0;
            $subtotal = $cantidad * $precio;

            // escribir en la ruta correcta del repeater
            $set("{$repeaterKey}.{$index}.precio_unitario", $precio);
            $set("{$repeaterKey}.{$index}.subtotal", $subtotal);

            $subtotalGeneral += $subtotal;
        }

        // subtotal general (campo raíz)
        $set('subtotal', $subtotalGeneral);

        self::recalcularAbonos($set, $get);
    }

    private static function recalcularFila(callable $set, callable $get): void
    {
        $productoId = $get('producto_id');
        $cantidad   = $get('cantidad') ?? 0;
        $precio     = $get('precio_unitario') ?? 0;
        
        $iva = $get('iva') ?? 0;
        $ivaFactor = 1 + ($iva / 100);
        $precioConIva = $precio * $ivaFactor;

        $subtotal = $cantidad * $precioConIva;
        $set('subtotal', $subtotal);

        // detectar clave del repeater para recalcular subtotal general
        $detalles = $get('../../detalles') ?? $get('../../detallesCompra') ?? $get('../../detalles_compra') ?? [];
        $totalPedido = collect($detalles)->sum(fn($d) => (float) ($d['subtotal'] ?? 0));
        $set('../../subtotal', $totalPedido);

        self::recalcularAbonos($set, $get);

        // actualizar código producto si corresponde
        //self::buscarCodigoProducto((int) $productoId);
    }

    private static function recalcularAbonos(callable $set, callable $get): void
    {
        // posibles nombres del repeater / lista de abonos en distintos formularios
        $abonosKeys = ['abonos', 'abonosCompra', 'abonos_compra', 'abonoCompra', 'abono_compra'];

        // posibles nombres de campo que contienen el importe del abono
        $montoKeys = ['monto', 'monto_abono_compra', 'monto_abono', 'amount', 'valor'];

        $basePath = null;
        $abonos = null;

        // encontrar la ruta donde está el array de abonos
        foreach (['', '../../', '../../../', '../../../../'] as $p) {
            foreach ($abonosKeys as $k) {
                try {
                    $maybe = $get($p . $k);
                } catch (\Throwable $e) {
                    $maybe = null;
                }
                if (! is_null($maybe)) {
                    $basePath = $p;
                    $abonos = $maybe;
                    break 2;
                }
            }
        }

        if ($abonos === null) {
            // no hay abonos en el formulario
            $basePath = $basePath ?? '';
            $abonos = [];
        }

        // sumar importes buscando cualquier nombre válido
        $totalAbonos = 0.0;
        foreach ($abonos as $abono) {
            foreach ($montoKeys as $mk) {
                if (isset($abono[$mk])) {
                    $totalAbonos += (float) $abono[$mk];
                    break;
                }
            }
        }

        // establecer el campo 'abono' en el contexto correcto (si existe)
        $currentAbono = (float) ($get($basePath . 'abono') ?? 0);
        if (round($currentAbono, 4) !== round($totalAbonos, 4)) {
            $set($basePath . 'abono', $totalAbonos);
        }

        // recalcular total a pagar (usa subtotal / descuento / abonos)
        $subtotal = (float) ($get($basePath . 'subtotal') ?? 0);
        $descuento = (float) ($get($basePath . 'descuento') ?? 0);
        $totalAPagar = $subtotal - $totalAbonos - $descuento;
        $totalAPagar = $totalAPagar < 0 ? 0 : $totalAPagar;

        $currentTotal = (float) ($get($basePath . 'total_a_pagar') ?? 0);
        if (round($currentTotal, 4) !== round($totalAPagar, 4)) {
            $set($basePath . 'total_a_pagar', $totalAPagar);
        }
    }

    /*private static function buscarCodigoProducto(int $productoId): string
    {
        $producto = Producto::find($productoId);
        return $producto ? $producto->codigo_producto : '-';
    }*/

    private static function recalcularDesdePrecioManual(callable $set, callable $get): void
    {
        $cantidad = (float) ($get('cantidad') ?? 0);
        $precio = (float) ($get('precio_unitario') ?? 0);

        $subtotal = $cantidad * $precio;
        $set('subtotal', $subtotal);

        // detectar clave del repeater para recalcular subtotal general
        $detalles = $get('../../detalles') ?? $get('../../detallesCompra') ?? $get('../../detalles_compra') ?? [];
        $totalPedido = collect($detalles)->sum(fn($d) => (float) ($d['subtotal'] ?? 0));
        $set('../../subtotal', $totalPedido);

        self::recalcularAbonos($set, $get);
    }
}
