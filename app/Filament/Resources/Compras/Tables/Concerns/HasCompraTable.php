<?php
namespace App\Filament\Resources\Compras\Tables\Concerns;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Actions\Action;

trait HasCompraTable
{
    /**
     * Configura la tabla común de Compras para reutilizar en varios Resources.
     */
    public static function configureComprasTable(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                CheckboxColumn::make('solicitado')
                    ->label('Solicitado')
                    ->sortable(),
                TextColumn::make('fecha')
                    ->label('Fecha Recibido')
                    ->date()
                    ->sortable(),
                TextColumn::make('factura')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('proveedor.nombre_proveedor')
                    ->label('Proveedor')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tipo_compra')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('metodo_pago')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('estado')
                    ->sortable()
                    ->searchable(),
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
                //
                //Filter::make('item_compra')

            ], layout: FiltersLayout::AboveContent)
            /*->recordActions([
                Action::make('edit')
                            ->label('Editar')
                            ->icon('heroicon-o-pencil')
                            ->url(fn($record) => route('filament.admin.resources.pedidos-pendientes.edit', ['record' => $record->getKey(), 'pedido_id' => $record->getKey()]))
                            ->openUrlInNewTab(false),
            ])*/
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
