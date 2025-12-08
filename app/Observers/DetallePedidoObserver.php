<?php

namespace App\Observers;

use App\Models\DetallePedido;
use App\Services\PedidoStockService;
use App\Services\DetallePedidoStockService;


class DetallePedidoObserver
{
    /**
     * Handle the DetallePedido "created" event.
     */
    public function created(DetallePedido $detallePedido): void
    {
        //
        app(DetallePedidoStockService::class)->creado($detallePedido);
    }

    /**
     * Handle the DetallePedido "updated" event.
     */
    public function updated(DetallePedido $detallePedido): void
    {
        //
        app(DetallePedidoStockService::class)->actualizado($detallePedido);

    }

    /**
     * Handle the DetallePedido "deleted" event.
     */
    public function deleted(DetallePedido $detallePedido): void
    {
        //
        app(DetallePedidoStockService::class)->eliminado($detallePedido);
    }
    
}
