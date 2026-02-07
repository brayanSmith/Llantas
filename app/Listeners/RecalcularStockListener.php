<?php

namespace App\Listeners;

use App\Events\StockActualizado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RecalcularStockListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StockActualizado $event): void
    {
        Log::info('RecalcularStockListener: productos actualizados', [
            'productos' => $event->productos,
            'bodegaId' => $event->bodegaId
        ]);
        // Recorrer los productos afectados y recalcular el stock de cada uno en la bodega indicada
        $service = app(\App\Services\StockCalculoService::class);
        foreach ($event->productos as $productoId) {
            $service->recalcularStockPorProductoYBodega($productoId, $event->bodegaId);
        }
    }
}
