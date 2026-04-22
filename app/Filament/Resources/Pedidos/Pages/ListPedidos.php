<?php

namespace App\Filament\Resources\Pedidos\Pages;

use App\Filament\Resources\Pedidos\PedidoResource;

use App\Filament\Traits\HasAbonoPedidoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ListPedidos extends ListRecords
{
    use HasAbonoPedidoAction;
    protected static string $resource = PedidoResource::class;

    public function getTabs(): array
{
    return [
        'TODOS' => Tab::make(),
            //->badge(fn () => \App\Models\Pedido::count()),

        'PENDIENTE' => Tab::make()
            ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('estado', 'PENDIENTE')),
            //->badge(fn () => \App\Models\Pedido::where('estado', 'PENDIENTE')->count()),

        'COMPLETADO' => Tab::make()
            ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('estado', 'COMPLETADO')),
           // ->badge(fn () => \App\Models\Pedido::where('estado', 'COMPLETADO')->count()),
    ];
}




    protected function getHeaderActions(): array
    {
        return [
            //$this->getAbonoPedidoAction(),

        ];
    }
}
