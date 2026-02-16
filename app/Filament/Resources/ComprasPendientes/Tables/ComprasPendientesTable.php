<?php

namespace App\Filament\Resources\ComprasPendientes\Tables;

use App\Filament\Resources\Compras\Tables\Concerns\HasCompraTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;

class ComprasPendientesTable
{
    use HasCompraTable;

    public static function configure(Table $table): Table
    {
        return self::configureComprasTable($table)
        ->recordActions([
            //EditAction::make(),
            Action::make('edit')
                ->label('Editar')
                ->icon('heroicon-o-pencil')
                ->url(fn($record) => route('filament.admin.resources.compras-pendientes.edit', ['record' => $record->getKey(), 'compra_id' => $record->getKey()]))
                ->openUrlInNewTab(false),
        ])
        //vamos hacer que solo nos traiga las que tengan estado pendiente
        ->modifyQueryUsing(fn($query) => $query->where('estado', 'PENDIENTE'));
    }
}
