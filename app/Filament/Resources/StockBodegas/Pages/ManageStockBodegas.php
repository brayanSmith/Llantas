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

            'Materia Prima' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria_producto', 'MATERIA_PRIMA')),
                //->badge(fn () => \App\Models\Pedido::where('estado', 'PENDIENTE')->count()),

            'Producto Terminado' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria_producto', 'PRODUCTO_TERMINADO')),
            // ->badge(fn () => \App\Models\Pedido::where('estado', 'FACTURADO')->count()),

            'Otro' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria_producto', 'OTRO')),
            // ->badge(fn () => \App\Models\Pedido::where('estado', 'ANULADO')->count()),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
