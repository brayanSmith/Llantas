<?php

namespace App\Filament\Resources\Comisions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\Comisions\Schemas\ComisionFormEstado;

class ComisionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('estado_comision')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'PENDIENTE' => 'warning',
                        'PAGADA' => 'success',
                        'RECHAZADA' => 'danger',
                        default => 'gray'
                    }),  
                TextColumn::make('vendedor.name')
                    ->sortable(),
                TextColumn::make('periodo_inicial')
                    ->date()
                    ->formatStateUsing(fn ($state) => date('d/m/Y', strtotime($state)))
                    ->sortable(),
                TextColumn::make('periodo_final')
                    ->date()
                    ->formatStateUsing(fn ($state) => date('d/m/Y', strtotime($state)))
                    ->sortable(),                            
                TextColumn::make('total_comision_neta')
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
                //EditAction::make(),
                DeleteAction::make(),
                ComisionFormEstado::getAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ComisionFormEstado::getBulkAction(),
                ]),
            ]);
    }
}
