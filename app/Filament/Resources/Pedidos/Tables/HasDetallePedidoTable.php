<?php

namespace App\Filament\Resources\Pedidos\Tables;

use Filament\Tables\Columns\TextColumn;

class HasDetallePedidoTable
{
    public static function tableColumns(): array
    {
        return [
            TextColumn::make('stock_total')
                ->label('Stock Total')
                ->badge()
                ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 3 ? 'success' : ($state > 0 ? 'warning' : 'danger'))),
            TextColumn::make('pedido_base_id')
                ->label('Código Pedido')
                ->searchable(query: fn ($query, $search) => $query->where('pedidos.id', 'like', "%{$search}%"))
                ->sortable(query: fn ($query, $direction) => $query->orderBy('pedidos.id', $direction)),
            TextColumn::make('cliente_nombre')
                ->label('Cliente')
                ->getStateUsing(fn ($record) => $record->cliente?->razon_social)
                ->searchable(query: fn ($query, $search) => $query->whereHas('cliente', fn ($q) => $q->where('razon_social', 'like', "%{$search}%")))
                ->sortable(query: fn ($query, $direction) => $query->orderBy('pedidos.cliente_id', $direction)),
            TextColumn::make('producto_nombre')
                ->label('Producto')
                ->searchable(query: fn ($query, $search) => $query->where('productos.concatenar_codigo_nombre', 'like', "%{$search}%")),
            TextColumn::make('fecha')
                ->label('Fecha')
                ->date()
                ->sortable(),
            TextColumn::make('cantidad')
                ->label('Cantidad'),
            TextColumn::make('precio_unitario')
                ->label('Precio Unitario')
                ->numeric(2, ',', '.'),
            TextColumn::make('costo_unitario')
                ->label('Costo Unitario')
                ->numeric(2, ',', '.'),
            TextColumn::make('costo_total')
                ->label('Costo Total')
                ->numeric(2, ',', '.'),
            TextColumn::make('ganancia_total')
                ->label('Ganancia Total')
                ->numeric(2, ',', '.'),
            TextColumn::make('subtotal')
                ->label('Subtotal')
                ->numeric(2, ',', '.'),
        ];
    }
}
