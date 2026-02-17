<?php

namespace App\Filament\Resources\AbonoCompras\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AbonoComprasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('compra_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fecha_abono_compra')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('monto_abono_compra')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('forma_pago_abono_compra')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('descripcion_abono_compra')
                    ->searchable(),
                TextColumn::make('imagen_abono_compra')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
