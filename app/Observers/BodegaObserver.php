<?php

namespace App\Observers;

use App\Models\Bodega;

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
        //
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
