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
                Select::make('formula')
                    ->relationship('formula', 'nombre_formula')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Fórmula'),                    
                TextInput::make('cantidad')
                    ->required()
                    ->numeric(),
                TextInput::make('lote')
                    ->required(),
                DatePicker::make('fecha_produccion')
                    ->required(),
                DatePicker::make('fecha_caducidad')
                    ->required(),
                Textarea::make('Observaciones')
                    ->default(null)
                    ->columnSpanFull(),
                Repeater::make('detalleProducciones')
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
                                titleAttribute: 'nombre_producto',
                                modifyQueryUsing: fn ($query) =>
                                    $query->where('categoria_producto', 'PRODUCTO_TERMINADO')
                                )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Producto Terminado'),
                        TextInput::make('cantidad')
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
                    ->label('Detalle de Materias Primas Utilizadas'),
                
            ]);
    }
}
