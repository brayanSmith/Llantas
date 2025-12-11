<?php

namespace App\Observers;

use App\Models\Bodega;
use App\Models\StockBodega;
use App\Services\ProductoStockService;

class BodegaObserver
{
    /**
     * Handle the Bodega "created" event.
     */
    public function created(Bodega $bodega): void
    {
        //
    }

    /**
     * Handle the Bodega "updated" event.
     */
    public function updated(Bodega $bodega): void
    {
        // Recalcular stock de todos los productos en esta bodega
        $productosIds = StockBodega::where('bodega_id', $bodega->id)
            ->pluck('producto_id')
            ->toArray();
        
        if (!empty($productosIds)) {
            app(ProductoStockService::class)->recalcularStockMasivo($productosIds, $bodega->id);
        }
    }

    /**
     * Handle the Bodega "deleted" event.
     */
    public function deleted(Bodega $bodega): void
    {
        //
    }

    /**
     * Handle the Bodega "restored" event.
     */
    public function restored(Bodega $bodega): void
    {
        //
    }

    /**
     * Handle the Bodega "force deleted" event.
     */
    public function forceDeleted(Bodega $bodega): void
    {
        //
    }
}
