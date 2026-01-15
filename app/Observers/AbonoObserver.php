<?php

namespace App\Observers;

use App\Models\Abono;
use App\Services\Pedido\PedidoCalculoService;

class AbonoObserver
{
    /**
     * Handle the Abono "created" event.
     */
    public function created(Abono $abono): void
    {
        //
        //PedidoCalculoService::obtenerVendedorDelAbono($abono);
    }

    /**
     * Handle the Abono "updated" event.
     */
    public function updated(Abono $abono): void
    {
        //
    }

    /**
     * Handle the Abono "deleted" event.
     */
    public function deleted(Abono $abono): void
    {
        //
    }

    /**
     * Handle the Abono "restored" event.
     */
    public function restored(Abono $abono): void
    {
        //
    }

    /**
     * Handle the Abono "force deleted" event.
     */
    public function forceDeleted(Abono $abono): void
    {
        //
    }
}
