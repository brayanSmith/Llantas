<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Services\PedidoStockService;
use App\Services\PedidoCalculoService;

class PedidoStockBodegaObserver
{

    public function creating(Pedido $pedido): void
    {
        // No hacer nada aquí, el pedido aún no tiene ID
    }

    /**
     * Handle the Pedido "created" event.
     */
    public function created(Pedido $pedido): void
    {
        $codigo = $pedido->setCodigoPedido();
        app(PedidoStockService::class)->creado($pedido);
    }

    /**
     * Handle the Pedido "updated" event.
     */
    public function updated(Pedido $pedido): void
    {
        //
        //$codigo = $pedido->setCodigoPedido();
        app(PedidoStockService::class)->actualizado($pedido);
    }

    /**
     * Handle the Pedido "deleted" event.
     */
    public function deleting(Pedido $pedido): void
    {
        //
        app(PedidoStockService::class)->eliminado($pedido);
    }
    
}
