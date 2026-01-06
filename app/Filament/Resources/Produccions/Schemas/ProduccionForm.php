<?php

namespace App\Filament\Resources\Produccions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use App\Models\Producto;
use Filament\Forms\Components\Placeholder;

use function Laravel\Prompts\text;

class ProduccionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('formula_id')
                    ->relationship('formula', 'nombre_formula')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Fórmula'),
                TextInput::make('cantidad')
                    ->default(1)
                    ->required()
                    ->numeric(),
                Select::make('bodega_id')
                    ->label('Bodega')
                    ->required()
                    ->relationship('bodega', 'nombre_bodega')
                    ->default(1)
                    ->searchable()
                    ->preload(),
                TextInput::make('lote')
                    ->required(),
                TextInput::make('ph')
                    ->required()
                    ->numeric()
                    ->step(0.01),
                TextInput::make('biscocidad')
                    ->required()
                    ->numeric(),
                TextInput::make('homogeneidad')
                    ->required()
                    ->numeric(),
                DatePicker::make('fecha_produccion')
                    ->default(today())
                    ->required(),
                DatePicker::make('fecha_caducidad')
                    ->default(today()->addDays(30))
                    ->required(),
                Select::make('responsable_lote_id')
                    ->label('Responsable de Lote')
                    ->relationship('responsableLote', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('responsable_cc_id')
                    ->label('Responsable de Control de Calidad')
                    ->relationship('responsableCC', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Textarea::make('Observaciones')
                    ->default(null)
                    ->columnSpanFull(),
                Repeater::make('detallesProduccionEntradas')
                    ->table([
                        TableColumn::make('Producto Terminado')->width('40%'),
                        TableColumn::make('cantidad')->width('10%'),
                        TableColumn::make('lote')->width('20%'),
                        TableColumn::make('fecha_produccion')->width('10%'),
                        TableColumn::make('observaciones')->width('40%'),
                    ])
                    ->compact()
                    ->relationship()
                    ->schema([
                        Select::make('producto_id')
                            ->relationship(
                                name: 'producto',
                                titleAttribute: 'concatenar_codigo_nombre',
                                modifyQueryUsing: fn ($query) =>
                                    $query->where('categoria_producto', 'PRODUCTO_TERMINADO')
                                )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Producto Terminado'),
                        TextInput::make('cantidad_producto')
                            ->default(1)
                            ->required()
                            ->numeric(),
                        TextInput::make('lote')
                            ->required(),
                        DatePicker::make('fecha_produccion')
                            ->default(today())
                            ->required(),
                        Textarea::make('observaciones')
                            ->default(null),
                    ])
                    ->minItems(1)
                    ->columnSpanFull()
                    ->label('Detalle Productos Producidos'),

                    Repeater::make('detallesProduccionSalidas')
                    ->table([
                        TableColumn::make('Producto')->width('50%'),
                        TableColumn::make('Cantidad')->width('10%'),
                        TableColumn::make('Costo')->width('20%'),
                        TableColumn::make('Total')->width('20%'),
                    ])
                    ->compact()
                    ->relationship()
                    ->schema([
                        Select::make('producto_id')
                            ->relationship(
                                name: 'producto',
                                titleAttribute: 'concatenar_codigo_nombre',
                                modifyQueryUsing: fn ($query) =>
                                    $query->where('categoria_producto', 'MATERIA_PRIMA')
                                )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->label('Materia Prima')
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $producto = Producto::find($get('producto_id'));
                                if ($producto) {
                                    $set('costo_producto', $producto->costo_producto);
                                    $set('total_costo', self::calcularTotalCosto($get));
                                }
                            }),
                        TextInput::make('cantidad_producto')
                            ->default(1)
                            ->required()
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $set('total_costo', self::calcularTotalCosto($get));
                            }),
                        TextInput::make('costo_producto')
                            ->label('Costo Unitario')
                            ->required()
                            ->numeric()
                            ->currencyMask(".", ",", 0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $set('total_costo', self::calcularTotalCosto($get));
                            }),

                        TextInput::make('total_costo')
                            ->currencyMask(".", ",", 0)
                            ->label('Costo Total')
                            ->required()
                            ->numeric(),

                    ])
                    ->minItems(1)
                    ->columnSpanFull()
                    ->label('Detalle Materia Prima Utilizada'),
            ]);
    }
    public static function calcularTotalCosto(callable $get): float
    {
        $cantidad = $get('cantidad_producto') ?? 0;
        $costo = $get('costo_producto') ?? 0;
        return $cantidad * $costo;
    }
}
