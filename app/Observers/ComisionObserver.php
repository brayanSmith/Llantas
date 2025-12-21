<?php

namespace App\Observers;

use App\Models\Comision;
use App\Services\ComisionService;

class ComisionObserver
{
    
    /**
     * Handle the Comision "created" event.
     */
    public function created(Comision $comision): void
    {
        // Ya no se agregan automáticamente los detalles
        // El usuario debe confirmar desde el formulario
    }

    /**
     * Handle the Comision "updated" event.
     */
    public function updated(Comision $comision): void
    {
        //
    }

    /**
     * Handle the Comision "deleted" event.
     */
    public function deleted(Comision $comision): void
    {
        //
    }

    /**
     * Handle the Comision "restored" event.
     */
    public function restored(Comision $comision): void
    {
        //
    }

    /**
     * Handle the Comision "force deleted" event.
     */
    public function forceDeleted(Comision $comision): void
    {
        //
    }
}
