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
                TextColumn::make('marca.marca')
                    ->label('Marca')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('referencia_producto')
                    ->label('Referencia')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('descripcion_producto')
                    ->label('Descripción')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('costo_producto')
                    ->label('Costo')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('valor_detal')
                    ->label('Detal')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('valor_mayorista')
                    ->label('Mayorista')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('valor_sin_instalacion')
                    ->label('Sin Instalación')
                    ->numeric(2)
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
