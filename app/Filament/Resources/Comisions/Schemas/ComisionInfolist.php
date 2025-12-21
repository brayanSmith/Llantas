<?php

namespace App\Filament\Resources\Comisions\Schemas;

use App\Filament\Infolists\Components\ComisionTable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;

class ComisionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Información General')
                            
                            ->schema([
                                ComisionTable::make('comision_details')
                                    ->label('Detalles de Comisión'),                                    
                            ]),
                        Tab::make('Pedidos Asociados')
                            ->badge(fn ($record) => $record->detallesComisionPedidos()->count())
                            ->schema([
                                RepeatableEntry::make('detallesComisionPedidos')
                                    ->label('Pedidos')
                                    ->table([
                                        TableColumn::make('Codigo')->width('20%'),
                                        TableColumn::make('Monto Total')->width('20%'),
                                        TableColumn::make('Fecha del Pedido')->width('20%'),
                                        TableColumn::make('Tipo de Venta')->width('20%'),
                                        TableColumn::make('Estado del Pedido')->width('20%'),
                                    ])                                
                                    ->schema([
                                        TextEntry::make('pedido.codigo')
                                        ->icon('heroicon-o-document-text')
                                        ->label('Remision'),                                        
                                        TextEntry::make('monto_venta')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->label('Monto Total')
                                        ->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.')),
                                        TextEntry::make('fecha_venta')
                                        ->icon('heroicon-o-calendar')
                                        ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('d/m/Y'))
                                        ->label('Fecha del Pedido'),
                                        TextEntry::make('tipo_venta')
                                        ->icon('heroicon-o-document-duplicate')
                                        ->label('Tipo de Venta'),
                                        TextEntry::make('pedido.estado')
                                        ->icon('heroicon-o-document-check')
                                        ->label('Estado del Pedido'),
                                    ]),
                            ]),
                        Tab::make('Abonos Asociados')
                            ->badge(fn ($record) => $record->detallesComisionAbonos()->count())
                            ->schema([
                                RepeatableEntry::make('detallesComisionAbonos')
                                    ->label('Abonos')
                                    ->table([
                                        TableColumn::make('Referencia')->width('30%'),
                                        TableColumn::make('Monto Abonado')->width('30%'),
                                        TableColumn::make('Fecha del Abono')->width('40%'),                                        
                                    ])                                
                                    ->schema([
                                        TextEntry::make('abonoPedido.pedido.codigo')
                                        ->icon('heroicon-o-receipt-refund')
                                        ->label('Pedido Referencia'),                                        
                                        TextEntry::make('monto_abono')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->label('Monto Abonado')
                                        ->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.')),
                                        TextEntry::make('fecha_abono')
                                        ->icon('heroicon-o-calendar')
                                        ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('d/m/Y'))
                                        ->label('Fecha del Abono'),                                        
                                    ]),
                            ]),
                        
                    ]),
            ]);
    }
}

