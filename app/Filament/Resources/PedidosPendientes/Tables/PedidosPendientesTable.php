<?php

namespace App\Filament\Resources\PedidosPendientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class PedidosPendientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->where('estado', 'PENDIENTE');

                // Si el usuario no es super_admin, mostrar solo sus pedidos
                if (!auth()->user()->hasRole('super_admin')) {
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
                TextColumn::make('cliente.ruta.ruta')
                    ->label('Ruta')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Vendedor')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('impresa')
                    ->label('Impresa'),
                TextColumn::make('tipo_venta')
                    ->label('Tipo Venta'),

                TextColumn::make('cliente.cuenta_total_pedidos_en_cartera')
                    ->label('Saldos')
                    ->sortable(),
                TextColumn::make('cliente.saldo_total_pedidos_en_cartera')
                    ->label('Saldo en Cartera')
                    ->money('COP', true,0,0)
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
