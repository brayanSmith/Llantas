<?php

namespace App\Filament\Resources\Produccions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

use Filament\Schemas\Schema;

class ProduccionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([       
                Section::make('Información de la Producción')
                    ->columns(2)
                    ->schema([         
                TextEntry::make('formula.nombre_formula')                    
                    ->numeric(),
                TextEntry::make('cantidad')
                    ->numeric(),
                TextEntry::make('lote'),
                    ]),
                Section::make('Fechas')
                    ->schema([
                TextEntry::make('fecha_produccion')
                    ->date(),
                TextEntry::make('fecha_caducidad')
                    ->date(),
                    ]),
                TextEntry::make('Observaciones')
                    ->placeholder('-')
                    ->columnSpanFull(),                
                RepeatableEntry::make('detallesProduccionEntradas')
                    ->label('Detalles Productos Producidos')
                    ->columnSpanFull()
                    ->table([
                        tableColumn::make('Producto Terminado')->width('40%'),
                        tableColumn::make('Cantidad')->width('10%'),
                        tableColumn::make('Medida')->width('10%'),
                        tableColumn::make('Lote')->width('10%'),
                        tableColumn::make('Fecha Producción')->width('10%'),
                        tableColumn::make('Observaciones')->width('40%'),
                    ])
                    ->schema([
                        TextEntry::make('producto.nombre_producto')
                            ->icon('heroicon-o-cube-transparent')
                            ->label('Producto Terminado'),
                        TextEntry::make('cantidad_producto')
                            ->icon('heroicon-o-calculator')
                            ->label('Cantidad')
                            ->color('success'),
                        TextEntry::make('producto.medida.nombre_medida')
                            ->icon('heroicon-o-calculator')                        
                            ->label('Medida'),
                        TextEntry::make('lote')
                            ->icon('heroicon-o-tag'),
                        TextEntry::make('fecha_produccion')
                            ->icon('heroicon-o-calendar')
                            ->date(),
                        TextEntry::make('observaciones')
                            ->icon('heroicon-o-chat-alt-2')
                            ->placeholder('-'),
                    ]),
                RepeatableEntry::make('detallesProduccionSalidas')
                    ->label('Detalles Materia Prima Utilizada')
                    ->columnSpanFull()
                    ->table([
                        tableColumn::make('Producto')->width('70%'),
                        tableColumn::make('Cantidad')->width('10%'),  
                        tableColumn::make('Medida')->width('20%'),                      
                    ])
                    ->schema([
                        TextEntry::make('producto.nombre_producto')
                            ->icon('heroicon-o-cube-transparent')
                            ->label('Producto'),
                        TextEntry::make('cantidad_producto')
                            ->icon('heroicon-o-calculator')
                            ->label('Cantidad')
                            ->color('danger'),               
                        TextEntry::make('producto.medida.nombre_medida')
                            ->icon('heroicon-o-calculator')                        
                            ->label('Medida'),         
                    ]),
            ]);
    }
}
