<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class Dashboard extends BaseDashboard
{
    // ...
    use HasFiltersForm;

   public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('bodega_id')
                            ->label('Bodega')
                            ->options(\App\Models\Bodega::pluck('nombre_bodega', 'id'))
                            ->preload(),
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                        Select::make('calculo')
                            ->options([
                                'cantidad' => 'Cantidad de Pedidos',
                                'valor' => 'Valor Total de Pedidos',
                            ])
                            ->default('valor')
                            ->label('Cálculo'),
                        Select::make('producto_id')
                            ->label('Producto')
                            ->options(\App\Models\Producto::pluck('concatenar_codigo_nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->multiple(),
                        /*Select::make('user_id')
                        ->label('Usuario')
                        ->options(\App\Models\User::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->multiple(),*/
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }
}
