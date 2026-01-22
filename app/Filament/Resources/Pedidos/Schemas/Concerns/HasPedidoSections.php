<?php

namespace App\Filament\Resources\Pedidos\Schemas\Concerns;

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
use App\Models\Cliente;
use Filament\Forms\Components\Checkbox;
use App\Services\VencimientoService;
use App\Services\ProximoAbonoService;
use App\Services\Pedido\PedidoCalculoService;
use Filament\Support\Enums\Alignment;

use function Livewire\Volt\on;

trait HasPedidoSections
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
                ->columnSpanFull()
    ];
    }

    // sección datos generales
    protected static function sectionDatosGenerales(bool $full = false, ?array $estadoOptions = null, string $defaultEstado = 'PENDIENTE'): array
    {
        // Opciones por defecto para el Toggle de estado (clave => etiqueta)
        $estadoOptions = $estadoOptions ?? [
            'PENDIENTE' => 'Pendiente',
            'FACTURADO' => 'Facturado',
            'EN_RUTA' => 'En Ruta',
            'ENTREGADO' => 'Entregado',
            'ANULADO'   => 'Anulado',
        ];

        // Mapeo de colores por defecto (clave => color)
        $estadoColors = [
            'PENDIENTE' => 'primary',
            'FACTURADO' => 'primary',
            'EN_RUTA' => 'primary',
            'ENTREGADO' => 'success',
            'ANULADO'   => 'danger',
        ];
        $section = Section::make('Datos del pedido')
            ->columns(4)
            ->schema([
                    TextInput::make('codigo')
                    ->disabled()
                    ->columnSpan(1)
                    ->label('Remisión'),

                    Select::make('cliente_id')
                        ->label('Cliente')
                        ->relationship('cliente', 'razon_social')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->reactive()
                        ->afterStateHydrated(fn($state, $set) => $set('retenedor_fuente_flag', $state ? (Cliente::find($state)?->retenedor_fuente === 'SI') : false))
                        ->afterStateUpdated(function ($state, $set) {
                            $set('retenedor_fuente_flag', $state ? (Cliente::find($state)?->retenedor_fuente === 'SI') : false);
                        })
                        ->columnSpan(3),

                    DatePicker::make('fecha')
                    ->label('Fecha de Facturación')
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

                    TextInput::make('ciudad')->default(null)->columnSpan(2),

                    TextInput::make('dias_plazo_vencimiento')
                        ->label('Días Plazo Vencimiento')
                        ->default(30)
                        ->numeric()
                        ->required()
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
                        ->live(onBlur: true)
                        ->minValue(0)
                        ->maxValue(365)
                        ->step(1)
                        ->columnSpan(2),

                    Select::make('metodo_pago')->options(['CREDITO' => 'Crédito', 'CONTADO' => 'Contado'])->default('CREDITO')->required()->columnSpan(2),

                    DatePicker::make('fecha_vencimiento')->label('Fecha de Vencimiento')->default(null)->columnSpan(2)->readOnly(),

                    ToggleButtons::make('tipo_precio')
                        ->options(['FERRETERO' => 'Ferretero', 'MAYORISTA' => 'Mayorista', 'DETAL' => 'Detal'])
                        ->default('DETAL')
                        ->grouped()
                        ->required()
                        ->reactive()
                        ->columnSpan(2)
                        ->afterStateUpdated(function ($state, $set, $get) {
                            // Recalcular todos los detalles con el nuevo tipo de precio
                            $detalles = $get('detalles') ?? [];
                            $detallesActualizados = PedidoCalculoService::calcularDatosProducto($detalles, $state);
                            $set('detalles', $detallesActualizados);

                            // Recalcular totales del pedido
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
                        }),

                    ToggleButtons::make('estado')
                        ->options($estadoOptions)
                        ->colors($estadoColors)
                        ->default($defaultEstado)
                        ->required()
                        ->columnSpan(4)
                        ->live()
                        ->grouped(),

                    Select::make('tipo_venta')->options([
                        'REMISIONADA' => 'Remisionada',
                        'ELECTRONICA' => 'Electrónica',
                    ])->required()->columnSpan(2),

                    Select::make('estado_venta')->options([
                        'VENTA' => 'Venta',
                        'DEVOLUCION' => 'Devolución',
                    ])->default('VENTA')->required()->columnSpan(2)->visible(fn($get) => $get('estado') === 'PENDIENTE'),

                    Select::make('user_id')
                        ->label('Vendedor')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'Comercial')/*->orWhere('name', 'super_admin')*/)
                        )
                        ->searchable()
                        ->required()
                        ->preload()
                        ->columnSpan(2),

                    Select::make('alistador_id')
                        ->label('Alistador')
                        ->relationship(
                            name: 'alistador',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'Logistica'))
                        )
                        ->searchable()
                        ->required(fn($get) => in_array($get('estado'), ['FACTURADO','EN_RUTA', 'ENTREGADO']))
                        ->preload()
                        ->columnSpan(2)
                        //->visible(fn($get) => in_array($get('estado'), ['FACTURADO','EN_RUTA', 'ENTREGADO']))
                        ->live(),
                    TextInput::make('fe')
                        ->label('F.E.')
                        ->visible(fn($get) => in_array($get('estado'), ['PENDIENTE'])),

                        // Toggle informativo (SI / NO). No se persiste en la BD.
                    Toggle::make('retenedor_fuente_flag')
                        ->label('Retenedor fuente')
                        ->disabled()               // solo informativo: el usuario no lo cambia aquí
                        ->dehydrated(false)        // no guardarlo en la BD
                        ->columnSpan(2),
                ]);

        if ($full) {
            $section->columnSpanFull();
        } else {
            $section->columnSpan(1);
        }

        return [$section];
    }

    // sección resumen
    protected static function sectionResumen(): array
    {
        return [
            Section::make('Resumen')
                ->schema([
                    Placeholder::make('subtotal_display')
                        ->label('Subtotal')
                        ->extraAttributes(['class' => 'text-lg font-semibold'])
                        ->content(function ($get) {
                            $subtotal = (float) ($get('subtotal') ?? 0);
                            return '$' . number_format($subtotal, 2, ',', '.');
                        }),

                    TextInput::make('subtotal')
                        ->hidden(),

                    Placeholder::make('abono_display')
                        ->label('Abono')
                        ->extraAttributes(['class' => 'text-lg font-semibold text-blue-600'])
                        ->content(function ($get) {
                            // Calcular desde el repeater de abonos si existe
                            $totalAbonos = (float) ($get('abono') ?? []);
                            return '$' . number_format($totalAbonos, 2, ',', '.');
                        }),

                    // Campo oculto para mantener el valor en BD
                    TextInput::make('abono')
                        ->hidden(),
                        //->dehydrated(true),

                    TextInput::make('descuento')
                        ->prefix('$')
                        ->currencyMask(".", ",", 2)
                        ->numeric()
                        ->live(onBlur: true)
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
                        }),

                    TextInput::make('flete')
                        ->prefix('$')
                        ->currencyMask(".", ",", 0)
                        ->numeric()
                        ->inputMode('decimal')
                        ->live(onBlur: true)
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
                        }),

                    Placeholder::make('total_a_pagar_display')
                        ->label('Total a pagar')
                        ->extraAttributes(['class' => 'text-lg font-semibold'])
                        ->content(function ($get) {
                            $totalAPagar = (float) ($get('total_a_pagar') ?? 0);
                            return '$' . number_format($totalAPagar, 2, ',', '.');
                        }),

                    Placeholder::make('saldo_pendiente_display')
                        ->label('Saldo pendiente')
                        ->extraAttributes(['class' => 'text-lg font-semibold text-red-600'])
                        ->content(function ($get) {
                            $saldoPendiente = (float) ($get('saldo_pendiente') ?? 0);
                            return '$' . number_format($saldoPendiente, 2, ',', '.');
                        }),

                    // Campo oculto para mantener el valor real del total a pagar (fijo)
                    TextInput::make('total_a_pagar')
                        ->hidden(),

                    // Campo oculto para mantener el saldo pendiente (se reduce con abonos)
                    TextInput::make('saldo_pendiente')
                        ->hidden(),
                ])->columnSpan(1),
        ];
    }

    //seccion primer comentario y segundo comentario
    protected static function sectionComentarios(): array
    {
        return [
            Section::make('Comentarios')
                ->columns(1)
                ->schema([
                    Textarea::make('primer_comentario')->label('Primer Comentario')->rows(2)->columnSpanFull(),
                    Textarea::make('segundo_comentario')->label('Segundo Comentario')->rows(2)->columnSpanFull(),
                ])->columnSpanFull()->collapsed(true)->collapsible(),
        ];
    }

    // sección detalles
    protected static function sectionDetalles(): array
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

    // sección abonos (visible solo facturado)
    protected static function sectionAbonos(): array
    {
        return [
            Section::make('Abonos')
                ->columnSpanFull()
                ->visible(fn($get) => in_array($get('estado'), ['FACTURADO', 'EN_RUTA', 'ENTREGADO', 'DEVUELTO']))
                ->schema([
                    Repeater::make('abonos')
                        ->relationship('abonoPedido')
                        ->label(function ($get) {
                            $abonos = $get('abonos') ?? [];
                            $total = collect($abonos)->sum(fn($abono) => (float) ($abono['monto'] ?? 0));
                            return 'Abonos realizados (Total: $' . number_format($total, 0, ',', '.') . ')';
                        })
                        ->schema([
                            Section::make('Datos del abono')
                            ->schema([
                                DateTimePicker::make('fecha')
                                    ->label('Fecha')
                                    ->required()
                                    ->default(now())
                                    ->columnSpan(1),
                                TextInput::make('monto')
                                    ->label('Monto')
                                    ->prefix('$')
                                    ->inputMode('decimal')
                                    ->currencyMask(".", ",", 0)
                                    ->required()
                                    ->stripCharacters('.')
                                    ->live(onBlur: true)
                                    ->numeric()
                                    ->columnSpan(1),
                                Select::make('forma_pago')
                                    ->label('Forma de pago')
                                    ->relationship(
                                        name: 'formaPago',
                                        titleAttribute: 'concatenar_subcuenta_concepto',
                                        modifyQueryUsing: fn ($query) => $query->where('tipo', 1)
                                    )
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->reactive()
                                    ->columnSpan(1),
                                Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->default(null)
                                    ->columnSpan(2),
                                Select::make('user_id')
                                    ->label('Usuario que registra')
                                    ->relationship('user', 'name')
                                    ->default(auth()->id())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                            ])->columns(3)->columnSpan(2),

                            Section::make('Soporte')->schema([
                                FileUpload::make('imagen')
                                    ->label('Comprobante o evidencia')
                                    ->directory('abonos')
                                    ->image()
                                    ->imagePreviewHeight('200')
                                    ->maxSize(2048)
                                    //->acceptedFileTypes(['image/*'])
                                    ->columnSpanFull(),
                            ])->columnSpan(1),
                        ])
                        ->addActionLabel('Añadir Abono')
                        ->columns(3)
                        ->columnSpan(4)
                        ->disabled(fn($get) => $get('estado') === 'ANULADO')
                        ->hidden(fn($get) => $get('estado') === 'ANULADO')
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
                        ->live(),
                ]),
        ];
    }

   //Seccion de Recibido
   private static function sectionRecibido(): array
    {
         return [
              Section::make('Recibido por')
                ->columns(1)
                ->schema([
                FileUpload::make('imagen_recibido')
                    ->image()
                    ->label('Imagen de recibido')
                    ->required(fn($get) => $get('estado') === 'ENTREGADO')
                    ->downloadable()
                    ->maxSize(1024),
                Select::make('motivo_devolucion')
                    ->label('Motivo de devolución')
                    ->required(fn($get) => $get('estado') === 'DEVUELTO')
                    ->visible(fn($get) => $get('estado') === 'DEVUELTO')
                    ->options([
                        'CERRADO' => 'Cerrado',
                        'TRASLADO' => 'Traslado',
                        'NO_CANCELA' => 'No cancela',
                        'NO_RECIBE' => 'No recibe',
                    ]),

                TextArea::make('comentario_entrega')
                    ->label('Comentario de entrega')
                    ->maxLength(500),
                ])->columnSpanFull(),
         ];
    }

}
