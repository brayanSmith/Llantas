<?php

namespace App\Observers;

use App\Models\DetallePedido;
use App\Services\Pedido\PedidoStockService;
use App\Services\DetallePedidoStockService;
use App\Services\Pedido\PedidoCalculoService;


class DetallePedidoObserver
{
    public function creating(DetallePedido $detallePedido): void
    {
        //
        $detallePedido->subtotal = PedidoCalculoService::calcularDetalles([
            'producto_id' => $detallePedido->producto_id,
            'cantidad' => $detallePedido->cantidad,
            'precio_unitario' => $detallePedido->precio_unitario,
            'aplicar_iva' => $detallePedido->aplicar_iva,
            'iva' => $detallePedido->iva,
        ]);
    }

    public function updating(DetallePedido $detallePedido): void
    {
        //
        $detallePedido->subtotal = PedidoCalculoService::calcularDetalles([
            'producto_id' => $detallePedido->producto_id,
            'cantidad' => $detallePedido->cantidad,
            'precio_unitario' => $detallePedido->precio_unitario,
            'aplicar_iva' => $detallePedido->aplicar_iva,
            'iva' => $detallePedido->iva,
        ]);
    }
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
