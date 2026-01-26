<?php

namespace App\Filament\Resources\PedidoCotizacions\Schemas;

use App\Models\Cliente;
use Filament\Schemas\Schema;
use App\Services\VencimientoService;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use App\Services\Pedido\PedidoCalculoService;
use App\Filament\Forms\Components\RepeaterPedido;
use App\Filament\Forms\Components\GeneralSectionPedido;

use function Laravel\Prompts\select;

class PedidoCotizacionDatosGenerales
{
    public static function sectionDatosGenerales(bool $full = false, ?array $estadoOptions = null, string $defaultEstado = 'PENDIENTE'): array
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
                    Radio::make('estado_venta')->options([
                        'COTIZACION' => 'Cotización',
                        'VENTA' => 'Venta',
                    ])
                    ->inline()
                    ->live()
                    ->default('VENTA')
                    ->required()->columnSpan(4)
                    ->visible(fn($get) => $get('estado') === 'PENDIENTE'),

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
                        ->required(fn($get) => $get('estado_venta') === 'VENTA')
                        ->visible(fn($get) => $get('estado_venta') === 'VENTA')
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

                    Select::make('metodo_pago')
                        ->options(['CREDITO' => 'Crédito', 'CONTADO' => 'Contado'])
                        ->default('CREDITO')
                        ->required(fn($get) => $get('estado_venta') === 'VENTA')
                        ->visible(fn($get) => $get('estado_venta') === 'VENTA')
                        ->columnSpan(2),

                    DatePicker::make('fecha_vencimiento')->label('Fecha de Vencimiento')->default(null)->columnSpan(2)->readOnly(),

                    ToggleButtons::make('tipo_precio')
                        ->options(['FERRETERO' => 'Ferretero', 'MAYORISTA' => 'Mayorista', 'DETAL' => 'Detal'])
                        ->default('DETAL')
                        ->grouped()
                        ->required(fn($get) => $get('estado_venta') === 'VENTA')
                        ->reactive()
                        ->columnSpan(2)
                        ->visible(fn($get) => $get('estado_venta') === 'VENTA')
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
                        ->hidden()
                        //->visible(fn($get) => $get('estado_venta') === 'VENTA')
                        ->grouped(),

                    Select::make('tipo_venta')->options([
                        'REMISIONADA' => 'Remisionada',
                        'ELECTRONICA' => 'Electrónica',
                    ])->columnSpan(2)
                    ->required(fn($get) => $get('estado_venta') === 'VENTA')
                    ->visible(fn($get) => $get('estado_venta') === 'VENTA'),

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
                        //->visible(fn($get) => $get('estado_venta') === 'VENTA')
                        ->searchable()
                        ->required(fn($get) => in_array($get('estado'), ['FACTURADO','EN_RUTA', 'ENTREGADO']) && $get('estado_venta') === 'VENTA')
                        ->preload()
                        ->columnSpan(2)
                        //->visible(fn($get) => in_array($get('estado'), ['FACTURADO','EN_RUTA', 'ENTREGADO']))
                        ->live(),
                    TextInput::make('fe')
                        ->label('F.E.')
                        ->visible(fn($get) => in_array($get('estado'), ['PENDIENTE']) && $get('estado_venta') === 'VENTA'),

                        // Toggle informativo (SI / NO). No se persiste en la BD.
                    Toggle::make('retenedor_fuente_flag')
                        ->label('Retenedor fuente')
                        ->visible(fn($get) => $get('estado_venta') === 'VENTA')
                        ->disabled()               // solo informativo: el usuario no lo cambia aquí
                        ->dehydrated(false)        // no guardarlo en la BD
                        ->columnSpan(2),

                    Select::make('bodega_id')
                        ->label('Bodega')
                        ->relationship('bodega', 'nombre_bodega')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->default(1)
                        ->columnSpan(2),

                    GeneralSectionPedido::make('general_section_pedido')->columnSpanFull(),

                    RepeaterPedido::make('detalles')->relationship('detalles')->products()->columnSpanFull(),
                    ]);



        if ($full) {
            $section->columnSpanFull();
        } else {
            $section->columnSpan(1);
        }

        return [$section];
    }
}
