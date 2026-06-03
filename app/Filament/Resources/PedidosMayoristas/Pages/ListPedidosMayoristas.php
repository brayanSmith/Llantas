<?php

namespace App\Filament\Resources\PedidosMayoristas\Pages;

use App\Filament\Resources\Pedidos\Pages\Concerns\InteractsWithDetallePedidosTab;
use App\Filament\Resources\PedidosMayoristas\PedidosMayoristaResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPedidosMayoristas extends ListRecords
{
    use InteractsWithDetallePedidosTab;

    protected static string $resource = PedidosMayoristaResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getTabs(): array
    {
        return [
            'pedidos' => Tab::make('Pedidos')
                ->icon('heroicon-o-shopping-cart')
                ->modifyQueryUsing(fn (Builder $query) => $query->with(['detalles.producto', 'cliente'])),
            'detalles' => Tab::make('Detalle Pedidos')
                ->icon('heroicon-o-list-bullet')
                ->modifyQueryUsing(fn (Builder $query) => $this->applyDetallePedidosTabQuery($query)),
        ];
    }
}
