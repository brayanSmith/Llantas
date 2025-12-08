<?php

namespace App\Filament\Traits;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use App\Models\Producto;
use App\Models\StockBodega;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;

trait HasProductosStockSection
{
    public static function getProductosStockSection(): Action
    {
        return Action::make('verStockProductos')
            ->label('Stock en Bodegas')
            ->icon('heroicon-o-archive-box')
            ->modalHeading(fn (Producto $record): string => "Stock en Bodegas para: {$record->nombre_producto}")
            //->modalWidth('lg')
            ->infolist([
                RepeatableEntry::make('stockBodegas')                    
                    ->table([
                        TableColumn::make('Bodega'),
                        TableColumn::make('Entradas'),
                        TableColumn::make('Salidas'),
                        TableColumn::make('Stock'),
                    ])
                    ->schema([
                        TextEntry::make('bodega.nombre_bodega'),
                        TextEntry::make('entradas'),
                        TextEntry::make('salidas'),
                        TextEntry::make('stock')
                            ->color(fn ($record) => $record->stock > 0 ? 'success' : ( $record->stock < 0 ? 'danger' : 'secondary' )),
                    ])
            ]);
    }
}