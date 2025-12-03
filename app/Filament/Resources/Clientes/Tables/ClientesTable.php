<?php

namespace App\Filament\Resources\Clientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;


class ClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ciudad')
                    ->searchable(),
                TextColumn::make('razon_social')
                    ->searchable(),
                // Vamos a poner los pedidos que estan en cartera
                TextColumn::make('total_cartera')
                    ->label('En Cartera')
                    //->badge()
                    //->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '$' . number_format($state, 0, ',', '.') : '$0'),
                // Llama la funcion getTotalVencidoAttribute() del modelo Cliente
                TextColumn::make('total_vencido')
                    ->label('Saldo Vencido')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '$' . number_format($state, 0, ',', '.') : '$0'),

                TextColumn::make('ruta.ruta')
                    ->label('Ruta')
                    ->searchable(),
                TextColumn::make('comercial.name')
                    ->label('Comercial')
                    ->searchable(),
                TextColumn::make('direccion')
                    ->searchable(),
                    
                
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalWidth('full'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
