<?php

namespace App\Filament\Resources\PedidoEnRutas\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ActionColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Enums\RecordActionsPosition;
use App\Filament\Tables\Columns\DescargarPdfColumn;
use App\Filament\Resources\Pedidos\Tables\HasPedidoTable;
use App\Filament\Resources\Pedidos\Tables\Concerns\HasActionSections;


class PedidoEnRutasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->whereIn('estado', ['FACTURADO'])
                    ->where('estado_venta', 'VENTA');

                // Si el usuario no es super_admin, mostrar solo sus pedidos
                /*if (!auth()->user()->hasRole(['super_admin', 'financiero', 'Financiero', 'Logistica', 'logistica'])) {
                    $query->where('user_id', auth()->id());
                }*/

                return $query;
            })
            ->groups([
                Group::make('fecha')
                    ->date()
                    ->collapsible(),
                Group::make('cliente.ruta.ruta')
                    ->collapsible(),

            ])->defaultGroup('cliente.ruta.ruta')
            ->columns([
                Split::make([
                    TextColumn::make('codigo')
                        ->getStateUsing(function ($record) {
                            $codigo = $record->codigo;
                            return $codigo ? "Código: {$codigo}" : 'Sin código';
                        })
                        ->searchable(),

                    TextColumn::make('cliente.ruta.ruta')
                        ->label('Ruta')
                        ->weight(FontWeight::Bold)
                        ->getStateUsing(function ($record) {
                            $ruta = $record->cliente?->ruta?->ruta;
                            return $ruta ? "Ruta: {$ruta}" : 'Sin ruta';
                        })
                        ->searchable()
                        ->sortable(),
                    SelectColumn::make('estado')
                        ->label('Estado')
                        ->options([
                            'EN_RUTA' => 'En Ruta',
                            'FACTURADO' => 'Facturado',
                        ])
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),


                ])->from('md')
            ])
            ->filters([
                //
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
            ->recordActions(
                [
                    ActionGroup::make([

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
