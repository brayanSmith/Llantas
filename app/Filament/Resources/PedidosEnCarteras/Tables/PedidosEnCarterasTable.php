<?php

namespace App\Filament\Resources\PedidosEnCarteras\Tables;

use App\Filament\Resources\Pedidos\Tables\HasDetallePedidoTable;
use App\Filament\Resources\Pedidos\Tables\HasPedidoFilters;
use App\Filament\Resources\Pedidos\Tables\HasPedidoTable;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PedidosEnCarterasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([25, 50, 100]) // Opciones de paginación
            ->defaultPaginationPageOption(100) // Por defecto 100 registros por página
            ->recordTitleAttribute('pedido_id')
            ->groups([
                \Filament\Tables\Grouping\Group::make('fecha')
                    ->label('Fecha del Pedido')
                    ->date()
                    ->collapsible(),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->whereHas('bodega', function ($q) {
                    $q->whereIn('nombre_bodega', ['Economi', 'Outlet']);
                })
                ->where('estado_pago', 'EN_CARTERA');
                //->where('estado', 'COMPLETADO');
                return $query;
            })
            ->columns([
                //
                // === Columnas tab Pedidos ===
                ...array_map(fn ($column) => $column->visible(fn ($livewire) => ($livewire->activeTab ?? 'pedidos') === 'pedidos'), HasPedidoTable::tableColumns()),

                // === Columnas tab Detalles ===
                ...array_map(fn ($column) => $column->visible(fn ($livewire) => ($livewire->activeTab ?? 'pedidos') === 'detalles'), HasDetallePedidoTable::tableColumns()),

            ])
            ->filters([
                TrashedFilter::make(),
                ...HasPedidoFilters::tableFilters(),
            ])  //layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('edit')
                    ->label(fn ($livewire) => ($livewire->activeTab ?? 'pedidos') === 'pedidos' ? 'Editar' : 'Ver Pedido')
                    ->icon(fn ($livewire) => ($livewire->activeTab ?? 'pedidos') === 'pedidos' ? 'heroicon-o-pencil' : 'heroicon-o-eye')
                    ->url(fn ($record, $livewire) => ($livewire->activeTab ?? 'pedidos') === 'detalles'
                        ? route('filament.admin.resources.pedidos-en-carteras.edit', ['record' => $record->pedido_base_id, 'pedido_id' => $record->pedido_base_id])
                        : route('filament.admin.resources.pedidos-en-carteras.edit', ['record' => $record->getKey(), 'pedido_id' => $record->getKey()])
                    )
                    ->openUrlInNewTab(false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha', 'desc');
    }
}
