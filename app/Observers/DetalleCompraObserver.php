<?php

namespace App\Observers;

use App\Models\DetalleCompra;
use App\Services\CompraStockService;
use App\Services\DetalleCompraStockService;
use App\Services\CompraCalculoService;

class DetalleCompraObserver
{
    /**
     * Calcular subtotal y asignar tipo antes de crear el registro
     */
    public function creating(DetalleCompra $detalle): void
    {
        /*$resultado = CompraCalculoService::calcularDetalles([
            'item_id' => $detalle->item_id,
            'descripcion_item' => $detalle->descripcion_item,
            'cantidad' => $detalle->cantidad,
            'precio_unitario' => $detalle->precio_unitario,
            'iva' => $detalle->iva,
        ]);

        // Asignar tipo_item basado en la compra
        $detalle->tipo_item = CompraCalculoService::esProductoGasto($detalle);
        $detalle->subtotal = $resultado['subtotal'];
        $detalle->precio_con_iva = $resultado['precio_con_iva'];*/
    }

    /**
     * Calcular subtotal y asignar tipo antes de actualizar el registro
     */
    public function updating(DetalleCompra $detalle): void
    {
        /*$resultado = CompraCalculoService::calcularDetalles([
            'item_id' => $detalle->item_id,
            'descripcion_item' => $detalle->descripcion_item,
            'cantidad' => $detalle->cantidad,
            'precio_unitario' => $detalle->precio_unitario,
            'iva' => $detalle->iva,
        ]);

        // Asignar tipo_item basado en la compra
        $detalle->tipo_item = CompraCalculoService::esProductoGasto($detalle);
        $detalle->subtotal = $resultado['subtotal'];
        $detalle->precio_con_iva = $resultado['precio_con_iva'];*/
    }

    public function created(DetalleCompra $detalle): void
    {
        //app(DetalleCompraStockService::class)->creado($detalle);
    }

    public function updated(DetalleCompra $detalle): void
    {
        //app(DetalleCompraStockService::class)->actualizado($detalle);
    }

    public function deleted(DetalleCompra $detalle): void
    {
        //app(DetalleCompraStockService::class)->eliminado($detalle);
    }
}
