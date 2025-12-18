<?php

namespace App\Observers;

use App\Models\DetalleProduccionSalida;
use App\Services\StockCalculoService;

class DetalleProduccionSalidaObserver
{
    /**
     * Handle the DetalleProduccionSalida "created" event.
     */
    public function created(DetalleProduccionSalida $detalleProduccionSalida): void
    {
        $this->recalcularStock($detalleProduccionSalida);
    }

    /**
     * Handle the DetalleProduccionSalida "updated" event.
     */
    public function updated(DetalleProduccionSalida $detalleProduccionSalida): void
    {
        $this->recalcularStock($detalleProduccionSalida);
    }

    /**
     * Handle the DetalleProduccionSalida "deleted" event.
     */
    public function deleted(DetalleProduccionSalida $detalleProduccionSalida): void
    {
        $this->recalcularStock($detalleProduccionSalida);
    }

    /**
     * Handle the DetalleProduccionSalida "restored" event.
     */
    public function restored(DetalleProduccionSalida $detalleProduccionSalida): void
    {
        $this->recalcularStock($detalleProduccionSalida);
    }

    /**
     * Handle the DetalleProduccionSalida "force deleted" event.
     */
    public function forceDeleted(DetalleProduccionSalida $detalleProduccionSalida): void
    {
        $this->recalcularStock($detalleProduccionSalida);
    }

    /**
     * Recalcula el stock del producto en la bodega correspondiente
     */
    private function recalcularStock(DetalleProduccionSalida $detalleProduccionSalida): void
    {
        // Obtener la bodega desde la producción
        $produccion = $detalleProduccionSalida->produccion;
        
        if ($produccion && $produccion->bodega_id) {
            $stockService = new StockCalculoService();
            $stockService->recalcularStockPorProductoYBodega(
                $detalleProduccionSalida->producto_id,
                $produccion->bodega_id
            );
        }
    }
}
