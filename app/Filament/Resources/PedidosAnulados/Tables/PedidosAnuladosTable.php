<?php

namespace App\Filament\Resources\PedidosAnulados\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\Pedidos\Tables\HasPedidoTable;

class PedidosAnuladosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->where('estado', 'ANULADO')
                ->where('estado_venta', 'VENTA');

                // Si el usuario no es super_admin, mostrar solo sus pedidos
                if (!auth()->user()->hasRole('super_admin', 'financiero')) {
                    $query->where('user_id', auth()->id());
                }

                return $query;
            })
            ->groups([
                Group::make('fecha')
                    ->date()
                    ->collapsible(),
                Group::make('cliente.ruta.ruta')
                    ->collapsible(),

            ])->defaultGroup('fecha')
            ->columns([
                ...HasPedidoTable::tableColumns(),
            ])
            ->filters([
                // Filtro por Ruta
                SelectFilter::make('cliente.ruta_id')
                    ->label('Ruta')
                    ->relationship('cliente.ruta', 'ruta')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Filtro por Cliente
                SelectFilter::make('cliente_id')
                    ->label(label: 'Cliente')
                    ->relationship('cliente', 'razon_social')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Filtro por Vendedor (solo visible para super_admin)
                SelectFilter::make('user_id')
                    ->label('Vendedor')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->visible(fn() => auth()->user()->hasRole('super_admin')),


            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn($record) => auth()->user()->can('Update:PedidosAnuladosResource')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
