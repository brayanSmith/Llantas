<?php

namespace App\Filament\Resources\PedidosOutlets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Filament\Resources\Pedidos\Tables\HasPedidoTable;
use Filament\Actions\Action;

class PedidosOutletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ...HasPedidoTable::tableColumns(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn($record) => route('filament.admin.resources.pedidos-outlets.edit', ['record' => $record->getKey(), 'pedido_id' => $record->getKey()]))
                    ->openUrlInNewTab(false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
