<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Services\PedidoStockService;

class PedidoStockBodegaObserver
{
    /**
     * Handle the Pedido "created" event.
     */
    public function created(Pedido $pedido): void
    {
        app(PedidoStockService::class)->creado($pedido);
    }

    /**
     * Handle the Pedido "updated" event.
     */
    public function updated(Pedido $pedido): void
    {
        //
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
