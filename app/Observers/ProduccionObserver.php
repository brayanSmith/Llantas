<?php

namespace App\Observers;

use App\Models\Produccion;
use App\Services\ProduccionService;

class ProduccionObserver
{
    /**
     * Handle the Produccion "created" event.
     */
    public function created(Produccion $produccion): void
    {
        //
        $produccionService = new ProduccionService();
        $produccionService->agregarDetalleProduccionSalida($produccion);
    }

    /**
     * Handle the Produccion "updated" event.
     */
    public function updated(Produccion $produccion): void
    {
        //
    }

    /**
     * Handle the Produccion "deleting" event.
     */
    public function deleting(Produccion $produccion): void
    {
        // Eliminar manualmente los detalles ANTES de que se elimine la producción
        // Esto dispara los observers de cada detalle para recalcular el stock
        $produccion->detallesProduccionSalidas()->each(function ($detalle) {
            $detalle->delete();
        });
        $produccion->detallesProduccionEntradas()->each(function ($detalle) {
            $detalle->delete();
        });
    }

    /**
     * Handle the Produccion "deleted" event.
     */
    public function deleted(Produccion $produccion): void
    {
        //
    }

    /**
     * Handle the Produccion "restored" event.
     */
    public function restored(Produccion $produccion): void
    {
        //
    }

    /**
     * Handle the Produccion "force deleted" event.
     */
    public function forceDeleted(Produccion $produccion): void
    {
        //
    }
}
