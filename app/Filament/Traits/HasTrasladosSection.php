<?php

namespace App\Filament\Traits;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\Traslado;
use App\Models\Bodega;
use App\Models\Producto;

trait HasTrasladosSection
{
    public static function getTrasladosSection(): Action
    {
        return Action::make('crearTraslado')
            ->label('Traslado')
            ->icon('heroicon-o-truck')            
            ->modalHeading(fn (Producto $record): string => "Crear Traslado para: {$record->nombre_producto}")
            ->modalWidth('md')
            ->form([
                Select::make('bodega_donante_id')
                    ->label('Bodega Donante')
                    // Asegúrate de usar el nombre de la relación en tu modelo (probablemente 'bodega')
                    ->relationship('traslados.bodegaDonante', 'nombre_bodega')
                    ->required(),
                
                Select::make('bodega_destino_id')
                    ->label('Bodega Destino')
                    // Asegúrate de usar el nombre de la relación en tu modelo (probablemente 'bodega')
                    ->relationship('traslados.bodegaDestino', 'nombre_bodega')
                    ->required(),
                    

                TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->rows(2),
            ])
            ->action(function ($record, array $data) {
                Traslado::create([
                    'producto_id'   => $record->id,
                    'bodega_donante_id' => $data['bodega_donante_id'],
                    'bodega_destino_id' => $data['bodega_destino_id'],
                    'cantidad'      => $data['cantidad'],
                    'observaciones' => $data['observaciones'] ?? null,
                ]);
            });
    }
}
