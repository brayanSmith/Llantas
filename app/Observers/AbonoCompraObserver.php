<?php

namespace App\Observers;

use App\Models\AbonoCompra;

class AbonoCompraObserver
{
    /**
     * Recalcular totales de la compra después de crear un abono
     */
    public function created(AbonoCompra $abono): void
    {
        $this->recalcularTotalesCompra($abono);
    }

    /**
     * Recalcular totales de la compra después de actualizar un abono
     */
    public function updated(AbonoCompra $abono): void
    {
        $this->recalcularTotalesCompra($abono);
    }

    /**
     * Recalcular totales de la compra después de eliminar un abono
     */
    public function deleted(AbonoCompra $abono): void
    {
        $this->recalcularTotalesCompra($abono);
    }

    /**
     * Recalcula los totales de la compra relacionada
     */
    private function recalcularTotalesCompra(AbonoCompra $abono): void
    {
        $compra = $abono->compra;
        
        if ($compra) {
            $compra->recalcularTotales();
        }
    }
}
