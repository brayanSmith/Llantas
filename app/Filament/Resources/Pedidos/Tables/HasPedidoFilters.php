<?php

namespace App\Filament\Resources\Pedidos\Tables;

use App\Models\Bodega;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;

trait HasPedidoFilters
{
    public static function tableFilters(): array
    {
        return [
            // Aquí puedes agregar los filtros específicos para la tabla de Pedidos
            Filter::make('pedido.bodega_id')
                ->label('Bodega')
                ->query(fn ($query, $data) => filled($data['value'] ?? null)
                    ? $query->where('bodega_id', $data['value'])
                    : $query)
                ->form([
                    Select::make('value')
                        ->label('Seleccionar Bodega')
                        ->placeholder('Todas las bodegas')
                        ->selectablePlaceholder()
                        ->default(null)
                        ->options(Bodega::pluck('nombre_bodega', 'id')),
                ]),
            Filter::make('pedido.estado_pago')
                ->label('Estado de Pago')
                ->query(fn ($query, $data) => filled($data['value'] ?? null)
                    ? $query->where('estado_pago', $data['value'])
                    : $query)
                ->form([
                    Select::make('value')
                        ->label('Seleccionar Estado de Pago')
                        ->placeholder('Todos los estados de pago')
                        ->selectablePlaceholder()
                        ->default(null)
                        ->options([
                            'SALDADO' => 'Saldado',
                            'EN_CARTERA' => 'En Cartera',
                        ]),
                ]),
        ];
    }
}
