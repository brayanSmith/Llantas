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
        $resultado = PedidoCalculoService::calcularDetalles([
            'producto_id' => $detallePedido->producto_id,
            'cantidad' => $detallePedido->cantidad,
            'precio_unitario' => $detallePedido->precio_unitario,
            'aplicar_iva' => $detallePedido->aplicar_iva,
            'iva' => $detallePedido->iva,
        ]);
        $detallePedido->subtotal = $resultado['subtotal'];
        $detallePedido->precio_con_iva = $resultado['precio_con_iva'];
    }

    public function updating(DetallePedido $detallePedido): void
    {
        //
        $resultado = PedidoCalculoService::calcularDetalles([
            'producto_id' => $detallePedido->producto_id,
            'cantidad' => $detallePedido->cantidad,
            'precio_unitario' => $detallePedido->precio_unitario,
            'aplicar_iva' => $detallePedido->aplicar_iva,
            'iva' => $detallePedido->iva,
        ]);
        $detallePedido->subtotal = $resultado['subtotal'];
        $detallePedido->precio_con_iva = $resultado['precio_con_iva'];
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
