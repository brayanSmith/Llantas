<?php

namespace App\Filament\Resources\Produccions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;

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
                DatePicker::make('fecha_produccion')
                    ->default(today())
                    ->required(),
                DatePicker::make('fecha_caducidad')
                    ->default(today()->addDays(30))
                    ->required(),
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
                            ->required(),
                        Textarea::make('observaciones')
                            ->default(null),
                    ])
                    ->minItems(1)
                    ->columnSpanFull()
                    ->label('Detalle Productos Producidos'),               
                
            ]);
    }
}
