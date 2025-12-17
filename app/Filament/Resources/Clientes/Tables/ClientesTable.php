<?php

namespace App\Filament\Resources\Clientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Support\Icons\Heroicon;

class ClientesTable
{
    public static function configure(Table $table): Table
    {   
        return $table
            ->modifyQueryUsing(function ($query) {
                // Si el usuario no es super_admin, mostrar solo sus clientes
                if (!auth()->user()->hasRole('super_admin')) {
                    $query->where('comercial_id', auth()->id());
                }
                
                return $query;
            })
            ->columns([
                Split::make([
                TextColumn::make('ciudad')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->icon(Heroicon::BuildingOffice2),
                TextColumn::make('razon_social')
                    ->searchable()
                    ->icon(Heroicon::UserGroup),
                // Vamos a poner los pedidos que estan en cartera
                TextColumn::make('total_cartera')
                    ->label('En Cartera')
                    ->icon(Heroicon::CurrencyDollar)
                    //->badge()
                    //->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '$' . number_format($state, 0, ',', '.') : '$0'),
                // Llama la funcion getTotalVencidoAttribute() del modelo Cliente
                TextColumn::make('total_vencido')
                    ->label('Saldo Vencido')
                    ->badge()
                    ->icon(Heroicon::CurrencyDollar)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '$' . number_format($state, 0, ',', '.') : '$0'),

                TextColumn::make('ruta.ruta')
                    ->icon(Heroicon::Map)
                    ->label('Ruta')
                    ->searchable(),
                TextColumn::make('comercial.name')
                    ->icon(Heroicon::User)
                    ->label('Comercial')
                    ->searchable(),
                TextColumn::make('direccion')
                    ->icon(Heroicon::HomeModern)
                    ->searchable(),
                    
                ])->from('md')
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
