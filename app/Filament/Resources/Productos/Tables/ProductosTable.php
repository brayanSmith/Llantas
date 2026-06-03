<?php

namespace App\Filament\Resources\Productos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use App\Models\Bodega;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Columns\ImageColumn;

use App\Filament\Traits\HasTrasladosSection;

class ProductosTable
{
    use HasTrasladosSection;
    public static function configure(Table $table): Table
    {
        $columns = [
            ImageColumn::make('imagen_producto')
                ->label('Imagen')
                ->disk('public')
                ->size(50)
                ->circular(),
            TextColumn::make('stock_bodegas_sum_stock')
                ->label('Stock Total')
                ->alignCenter()
                ->badge()
                ->sortable()
                ->getStateUsing(fn ($record) => (int) ($record->stock_bodegas_sum_stock ?? 0))
                ->color(fn ($state) => ((int) $state) > 0 ? 'success' : 'danger'),

            TextColumn::make('marca.marca')
                ->label('Marca')
                ->sortable()
                ->searchable(),
            TextColumn::make('referencia_producto')
                ->label('Referencia')
                ->sortable()
                ->searchable(),
            TextColumn::make('pedidos_consignacion')
                ->label('Consignación')
                ->alignCenter()
                ->badge()
                ->searchable()
                ->sortable()
                ->getStateUsing(fn ($record) => (int) ($record->pedidos_consignacion ?? 0))
                ->color(fn ($state) => ((int) $state) > 0 ? 'warning' : 'gray'),

            TextColumn::make('pedidos_cartera')
                ->label('En Cartera')
                ->alignCenter()
                ->badge()
                ->searchable()
                ->sortable()
                ->getStateUsing(fn ($record) => (int) ($record->pedidos_cartera ?? 0))
                ->color(fn ($state) => ((int) $state) > 0 ? 'warning' : 'gray'),
            TextColumn::make('descripcion_producto')
                ->label('Descripción')
                ->sortable()
                ->searchable(),
            TextColumn::make('costo_producto')
                ->label('Costo')
                ->color('primary')
                ->numeric(2)
                ->sortable(),
            TextColumn::make('valor_detal')
                ->label('Detal')
                ->color('success')
                ->numeric(2)
                ->sortable(),
            TextColumn::make('valor_mayorista')
                ->label('Mayorista')
                ->color('warning')
                ->numeric(2)
                ->sortable(),
            TextColumn::make('valor_sin_instalacion')
                ->label('Sin Instalación')
                ->color('danger')
                ->numeric(2)
                ->sortable(),
        ];

        foreach (Bodega::query()->orderBy('nombre_bodega')->get() as $bodega) {
            $columns[] = TextColumn::make("stock_bodega_{$bodega->id}")
                ->label($bodega->nombre_bodega)
                ->alignCenter()
                ->badge()
                ->getStateUsing(fn ($record) => (int) $record->stockBodegas
                    ->where('bodega_id', $bodega->id)
                    ->sum('stock'))
                ->color(fn ($state) => ((int) $state) > 0 ? 'success' : 'danger');
        }

        $columns[] = TextColumn::make('created_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);

        $columns[] = TextColumn::make('updated_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['stockBodegas:id,producto_id,bodega_id,stock'])
                ->withSum('stockBodegas', 'stock')
                ->withSum([
                    'detallePedidos as pedidos_consignacion' => fn ($detallePedidosQuery) => $detallePedidosQuery
                        ->whereHas('pedido', fn ($pedidoQuery) => $pedidoQuery
                            ->where('estado_pago', 'EN_CARTERA')
                            ->where('tipo_precio', 'MAYORISTA')),
                    'detallePedidos as pedidos_cartera' => fn ($detallePedidosQuery) => $detallePedidosQuery
                        ->whereHas('pedido', fn ($pedidoQuery) => $pedidoQuery
                            ->where('estado_pago', 'EN_CARTERA')
                            ->where('tipo_precio', 'DETAL')),
                ], 'cantidad'))
            ->columns($columns)
            ->filters([
                //
            ])
            ->actions([
                HasTrasladosSection::getTrasladosSection(),
                EditAction::make(),
            ], position: RecordActionsPosition::BeforeColumns)

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                    ->label('Eliminar Seleccionados'),
                ]),
            ]);
    }
}
