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
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                        Select::make('calculo')
                            ->options([
                                'cantidad' => 'Cantidad de Pedidos',
                                'valor' => 'Valor Total de Pedidos',
                            ])
                            ->default('valor')
                            ->label('Cálculo'),
                        Select::make('user_id')
                        ->label('Usuario')
                        ->options(\App\Models\User::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->multiple(),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }
}
