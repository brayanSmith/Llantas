<?php

namespace App\Filament\Resources\PedidosEstadoPagoEnCarteras\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\RecordActionsPosition;
use App\Filament\Resources\Pedidos\Tables\HasPedidoTable;
use App\Filament\Resources\Pedidos\Tables\Concerns\HasActionSections;

class PedidosEstadoPagoEnCarterasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            //voy a traer los pedidos que esten en estado_pago EN_CARTERA y estado FACTURADO
            ->modifyQueryUsing(function ($query) {
                $query->where('estado_pago', 'EN_CARTERA')->whereIn('estado', ['FACTURADO', 'EN_RUTA', 'ENTREGADO'])->where('estado_venta', 'VENTA');

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
                ActionGroup::make([
                    HasActionSections::registrarAbonoAction(),
                    ViewAction::make()
                        ->modalWidth('full'),
                    EditAction::make(),
            ]),
        ],
        position: RecordActionsPosition::BeforeColumns
    )
            ->toolbarActions([
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
                ]),
            ]);
    }
}

