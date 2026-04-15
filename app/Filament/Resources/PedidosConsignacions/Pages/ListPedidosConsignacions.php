<?php

namespace App\Filament\Resources\PedidosConsignacions\Pages;

use App\Filament\Resources\PedidosConsignacions\PedidosConsignacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListPedidosConsignacions extends ListRecords
{
    protected static string $resource = PedidosConsignacionResource::class;

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
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->join('detalle_pedidos', 'pedidos.id', '=', 'detalle_pedidos.pedido_id')
                    ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
                    ->select([
                        'detalle_pedidos.id as id',
                        'pedidos.id as pedido_id',
                        'pedidos.fecha',
                        'pedidos.cliente_id',
                        'detalle_pedidos.producto_id',
                        'detalle_pedidos.cantidad',
                        'detalle_pedidos.precio_unitario',
                        'detalle_pedidos.costo_unitario',
                        'detalle_pedidos.costo_total',
                        'detalle_pedidos.ganancia_total',
                        'detalle_pedidos.subtotal',
                        'productos.concatenar_codigo_nombre as producto_nombre',
                        DB::raw('(SELECT COALESCE(SUM(stock_bodegas.stock), 0) FROM stock_bodegas WHERE stock_bodegas.producto_id = detalle_pedidos.producto_id) as stock_total'),
                    ])
                    ->with(['cliente'])
                ),
        ];
    }
}
