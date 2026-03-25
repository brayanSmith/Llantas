<?php

namespace App\Observers;

use App\Models\Compra;
use App\Services\CompraStockService;

class CompraStockBodegaObserver
{
    /**
     * ✅ Cuando se CREA la compra
     */
    public function created(Compra $compra): void
    {
        //$compra->recalcularTotales();
        app(CompraStockService::class)->crearProductosBodega($compra);
        app(CompraStockService::class)->creado($compra);
        //$compra->setEstadoPago();

    }

    public function updated(Compra $compra): void
    {
        //app(CompraStockService::class)->crearProductosBodega($compra);
        /*app(CompraStockService::class)->actualizado($compra);
        $compra->setEstadoPago();*/
    }

    public function deleting(Compra $compra): void
    {
        app(CompraStockService::class)->eliminado($compra);
    }
}
