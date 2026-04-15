<?php

namespace App\Filament\Tables;

use App\Models\DetallePedido;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TablePedidosPorDetalle
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => DetallePedido::query())
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
