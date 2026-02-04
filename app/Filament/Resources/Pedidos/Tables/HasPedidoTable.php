<?php

namespace App\Filament\Resources\Pedidos\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Tables\Columns\DescargarPdfColumn;

class HasPedidoTable
{
    public static function tableColumns(): array
    {
        return [
            TextColumn::make('estado')
                ->label('Estado')
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Creación')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('codigo')
                ->label('Remisión')
                ->searchable(),
            DescargarPdfColumn::make('descargar_pdf')
                ->label('Pdf'),
            ToggleColumn::make('impresa')
                ->label('Impresa'),
            TextColumn::make('cliente.razon_social')
                ->label('Cliente')
                ->searchable()
                ->sortable(),

            TextColumn::make('cliente.saldo_total_pedidos_en_cartera')
                ->label('Saldo en Cartera')
                ->numeric(2, ",", ".", 2)
                //->money('COP', true,0,2)
                ->badge()
                ->color('warning')
                ->sortable(),
            TextColumn::make('cliente.saldo_total_pedidos_vencidos')
                ->label('Saldo Vencido')
                ->numeric(2, ",", ".", 2)
                //->money('COP', true,0,2)
                ->badge()
                ->color('danger')
                ->sortable(),
            TextColumn::make('tipo_venta')
                ->label('Tipo Venta'),
            TextColumn::make('total_a_pagar')
                ->label('Total a Pagar')
                ->numeric(2, ",", ".", 2)

                //->money('COP', true,0,2)
                ->sortable(),
            TextColumn::make('cliente.ruta.ruta')
                ->label('Ruta')
                ->sortable(),
            TextColumn::make('user.name')
                ->label('Vendedor')
                ->searchable()
                ->sortable(),

            TextColumn::make('fecha')
                ->label('Fecha de Facturación')
                ->dateTime()
                ->sortable(),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
