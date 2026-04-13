<?php

namespace App\Filament\Resources\Compras\Tables;

use Filament\Tables\Table;
use App\Filament\Resources\Compras\Tables\Concerns\HasCompraTable;
use Filament\Actions\Action;

class ComprasTable
{
    use HasCompraTable;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                ...HasCompraTable::tableColumns(),
            ])
        ->recordActions([
            //EditAction::make(),
            Action::make('edit')
                ->label('Editar')
                ->icon('heroicon-o-pencil')
                ->url(fn($record) => route('filament.admin.resources.compras.edit', ['record' => $record->getKey(), 'compra_id' => $record->getKey()]))
                ->openUrlInNewTab(false),
        ]);
    }
}
