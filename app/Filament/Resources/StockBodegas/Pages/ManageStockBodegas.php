<?php

namespace App\Filament\Resources\StockBodegas\Pages;

use App\Filament\Resources\StockBodegas\StockBodegaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Tabs;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ManageStockBodegas extends ManageRecords
{
    protected static string $resource = StockBodegaResource::class;

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make(),
                //->badge(fn () => \App\Models\Pedido::count()),

            'Llantas' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria', 'LLANTA')),
                //->badge(fn () => \App\Models\Pedido::where('estado', 'PENDIENTE')->count()),

            'Rines' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria', 'RIN')),
            // ->badge(fn () => \App\Models\Pedido::where('estado', 'FACTURADO')->count()),
            'Servicios' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria', 'SERVICIO')),

            'Otros' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria', 'OTRO')),
            // ->badge(fn () => \App\Models\Pedido::where('estado', 'ANULADO')->count()),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
