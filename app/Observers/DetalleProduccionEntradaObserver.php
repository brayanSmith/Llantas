<?php

namespace App\Observers;

use App\Models\DetalleProduccionEntrada;
use App\Services\StockCalculoService;

class DetalleProduccionEntradaObserver
{
    /**
     * Handle the DetalleProduccionEntrada "created" event.
     */
    public function created(DetalleProduccionEntrada $detalleProduccionEntrada): void
    {
        //
        $this->recalcularStock($detalleProduccionEntrada);
    }

    /**
     * Handle the DetalleProduccionEntrada "updated" event.
     */
    public function updated(DetalleProduccionEntrada $detalleProduccionEntrada): void
    {
        //
        $this->recalcularStock($detalleProduccionEntrada);
    }

    /**
     * Handle the DetalleProduccionEntrada "deleted" event.
     */
    public function deleted(DetalleProduccionEntrada $detalleProduccionEntrada): void
    {
        //
        $this->recalcularStock($detalleProduccionEntrada);
    }

    /**
     * Handle the DetalleProduccionEntrada "restored" event.
     */
    public function restored(DetalleProduccionEntrada $detalleProduccionEntrada): void
    {
        //
        $this->recalcularStock($detalleProduccionEntrada);
    }

    /**
     * Handle the DetalleProduccionEntrada "force deleted" event.
     */
    public function forceDeleted(DetalleProduccionEntrada $detalleProduccionEntrada): void
    {
        //
        $this->recalcularStock($detalleProduccionEntrada);
    }

    /**
     * Recalcula el stock del producto en la bodega correspondiente
     */
    private function recalcularStock(DetalleProduccionEntrada $detalleProduccionEntrada): void
    {
        // Obtener la bodega desde la producción
        $produccion = $detalleProduccionEntrada->produccion;

        if ($produccion && $produccion->bodega_id) {
            $stockService = new StockCalculoService();
            $stockService->recalcularStockPorProductoYBodega(
                $detalleProduccionEntrada->producto_id,
                $produccion->bodega_id,
            );
        }
    }

}
