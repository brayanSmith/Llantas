<?php

namespace App\Filament\Resources\PedidosMayoristas\Tables;

use App\Filament\Resources\Pedidos\Tables\HasPedidoTable;
use App\Filament\Resources\Pedidos\Tables\HasDetallePedidoTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;

class PedidosMayoristasTable
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
                $query->where('tipo_precio', 'MAYORISTA')
                ->where('estado', 'COMPLETADO');
                return $query;
            })
            ->columns([
                // === Columnas tab Pedidos ===
                ...array_map(fn ($column) => $column->visible(fn ($livewire) => ($livewire->activeTab ?? 'pedidos') === 'pedidos'), HasPedidoTable::tableColumns()),

                // === Columnas tab Detalles ===
                ...array_map(fn ($column) => $column->visible(fn ($livewire) => ($livewire->activeTab ?? 'pedidos') === 'detalles'), HasDetallePedidoTable::tableColumns()),
            ])
            ->filters([
                TrashedFilter::make(),
            ])

             ->recordActions([
                Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn($record) => route('filament.admin.resources.pedidos-mayoristas.edit', ['record' => $record->getKey(), 'pedido_id' => $record->getKey()]))
                    ->openUrlInNewTab(false)
                    ->visible(fn ($livewire) => ($livewire->activeTab ?? 'pedidos') === 'pedidos'),
                Action::make('ver_pedido')
                    ->label('Ver Pedido')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.pedidos-mayoristas.edit', ['record' => $record->pedido_id, 'pedido_id' => $record->pedido_id]))
                    ->openUrlInNewTab(false)
                    ->visible(fn ($livewire) => ($livewire->activeTab ?? 'pedidos') === 'detalles'),
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
