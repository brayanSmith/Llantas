<?php

namespace App\Filament\Resources\PedidosEconomics\Pages;

use App\Filament\Resources\Pedidos\Pages\Concerns\InteractsWithDetallePedidosTab;
use App\Filament\Resources\PedidosEconomics\PedidosEconomicResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPedidosEconomics extends ListRecords
{
    use InteractsWithDetallePedidosTab;

    protected static string $resource = PedidosEconomicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
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
