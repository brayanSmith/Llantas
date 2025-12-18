<?php

namespace App\Filament\Resources\Productos\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\RecordActionsPosition;

use App\Filament\Traits\HasTrasladosSection;

class ProductosTable
{
    use HasTrasladosSection;
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen_producto')
                    ->label('Imagen')
                    ->disk('public')
                    ->size(50)
                    ->circular(),
                TextColumn::make('ubicacion_producto')
                    ->label('Ubicación')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('codigo_producto')
                    ->label('Código')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nombre_producto')
                    ->sortable()
                    ->searchable(),                
                TextColumn::make('costo_producto')
                    ->label('Costo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('valor_detal_producto')
                    ->label('Detal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('valor_mayorista_producto')
                    ->label('Mayorista')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('valor_ferretero_producto')
                    ->label('Ferretero')
                    ->numeric()
                    ->sortable(),                

                TextColumn::make('Bodega.nombre_bodega')
                    ->label('Bodega')
                    ->sortable(),

                TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->label('Stock')
                    ->color(function ($record) {
                        if ($record->stock <= $record->alerta_producto) {
                            return 'danger';
                        }
                        return 'success';
                    }),
                
                TextColumn::make('entradas')
                    ->label('Entradas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('salidas')
                    ->label('Salidas')
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
