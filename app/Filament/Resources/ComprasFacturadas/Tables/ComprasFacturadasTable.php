<?php

namespace App\Filament\Resources\ComprasFacturadas\Tables;

use App\Filament\Resources\Compras\Tables\Concerns\HasCompraTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;

class ComprasFacturadasTable
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
                ->url(fn($record) => route('filament.admin.resources.compras-facturadas.edit', ['record' => $record->getKey(), 'compra_id' => $record->getKey()]))
                ->openUrlInNewTab(false),
        ])
         ->modifyQueryUsing(fn($query) => $query->where('estado', 'FACTURADO'));
    }
}
