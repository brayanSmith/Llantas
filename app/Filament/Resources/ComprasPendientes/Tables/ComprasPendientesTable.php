<?php

namespace App\Filament\Resources\ComprasPendientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Filament\Resources\Compras\Tables\Concerns\HasCompraTable;
use Filament\Actions\Action;

class ComprasPendientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->where('estado', 'PENDIENTE');
                return $query;
            })
            ->columns([
                //
                ...HasCompraTable::tableColumns(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                //EditAction::make(),
                Action::make('edit')
                ->label('Editar')
                ->icon('heroicon-o-pencil')
                ->url(fn($record) => route('filament.admin.resources.compras-pendientes.edit', ['record' => $record->getKey(), 'compra_id' => $record->getKey()]))
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
