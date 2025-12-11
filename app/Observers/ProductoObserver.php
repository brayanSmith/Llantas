<?php

namespace App\Observers;

use App\Models\Producto;
use App\Services\ProductoStockService;

class ProductoObserver
{
    /**
     * Handle the Producto "created" event.
     */
    public function created(Producto $producto): void
    {
        app(ProductoStockService::class)->crearProductosBodega($producto);
        app(ProductoStockService::class)->recalcularStockTodasBodegas($producto->id);
    }

    public function updated(Producto $producto): void
    {
        // Recalcular stock en todas las bodegas del producto
        app(ProductoStockService::class)->recalcularStockTodasBodegas($producto->id);
    }

    /**
     * Handle the Producto "deleted" event.
     */
    public function deleted(Producto $producto): void
    {
        //
    }

    /**
     * Handle the Producto "restored" event.
     */
    public function restored(Producto $producto): void
    {
        //
    }

    /**
     * Handle the Producto "force deleted" event.
     */
    public function forceDeleted(Producto $producto): void
    {
        //
    }
}
