<?php

namespace App\Filament\Resources\PedidosEnCarteras\Pages;

use App\Filament\Resources\Pedidos\Pages\Concerns\InteractsWithDetallePedidosTab;
use App\Filament\Resources\PedidosEnCarteras\PedidosEnCarteraResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPedidosEnCarteras extends ListRecords
{
    use InteractsWithDetallePedidosTab;

    protected static string $resource = PedidosEnCarteraResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // CreateAction::make(),
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
