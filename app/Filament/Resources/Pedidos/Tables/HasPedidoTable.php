<?php

namespace App\Filament\Resources\Pedidos\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Tables\Columns\DescargarPdfColumn;
use Dom\Text;

class HasPedidoTable
{
    public static function tableColumns(): array
    {
        return [
            DescargarPdfColumn::make('descargar_pdf')
                ->label('Pdf'),
            TextColumn::make('id')
                ->label('ID')
                ->sortable(),
            TextColumn::make('cliente.razon_social')
                ->label('Cliente')
                ->searchable()
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Creación')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('estado')
                ->label('Estado')
                ->sortable(),
            TextColumn::make('total_a_pagar')
                ->label('Total a Pagar')
                ->numeric(2, ",", ".", 2)
                //->money('COP', true,0,2)
                ->sortable(),
            TextColumn::make('user.name')
                ->label('Vendedor')
                ->searchable()
                ->sortable(),
            TextColumn::make('turno')
                ->label('Turno')
                ->searchable()
                ->sortable(),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
