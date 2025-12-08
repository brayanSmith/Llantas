<?php

namespace App\Observers;

use App\Models\Traslado;
use App\Services\TrasladoService;

class TrasladoObserver
{
    /**
     * Handle the Traslado "created" event.
     */
    public function created(Traslado $traslado): void
    {
        //
        app(TrasladoService::class)->crearProductosBodega($traslado);
        app(TrasladoService::class)->creado($traslado);
    }

    /**
     * Handle the Traslado "updated" event.
     */
    public function updated(Traslado $traslado): void
    {
        //
        app(TrasladoService::class)->crearProductosBodega($traslado);
        app(TrasladoService::class)->actualizado($traslado);
    }

    /**
     * Handle the Traslado "deleted" event.
     */
    public function deleted(Traslado $traslado): void
    {
        //
        app(TrasladoService::class)->eliminado($traslado);
    }

    /**
     * Handle the Traslado "restored" event.
     */
    public function restored(Traslado $traslado): void
    {
        //
    }

    /**
     * Handle the Traslado "force deleted" event.
     */
    public function forceDeleted(Traslado $traslado): void
    {
        //
    }
}
