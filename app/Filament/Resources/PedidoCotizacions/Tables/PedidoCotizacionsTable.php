<?php

namespace App\Filament\Resources\PedidoCotizacions\Tables;

use Dom\Text;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Column;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;

class PedidoCotizacionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->where('estado', 'PENDIENTE')
                ->where('estado_venta', 'COTIZACION');

                // Si el usuario no es super_admin, mostrar solo sus pedidos
                if (!auth()->user()->hasRole('super_admin')) {
                    $query->where('user_id', auth()->id());
                }

                return $query;
            })
            ->columns([
                //
                TextColumn::make('fecha')
                    ->label('Fecha de Facturación')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('codigo')
                    ->label('Remisión')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cliente.razon_social')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_a_pagar')
                    ->label('Total a Pagar')
                    ->money('COP', true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => route('filament.admin.resources.pedido-cotizacions.edit', ['record' => $record->getKey(), 'pedido_id' => $record->getKey()]))
                    ->openUrlInNewTab(false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
