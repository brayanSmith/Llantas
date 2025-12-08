<?php

namespace App\Observers;

use App\Models\DetalleCompra;
use App\Services\CompraStockService;
use App\Services\DetalleCompraStockService;

class DetalleCompraObserver
{
    public function created(DetalleCompra $detalle): void
    {
        app(DetalleCompraStockService::class)->creado($detalle);
    }

    public function updated(DetalleCompra $detalle): void
    {
        app(DetalleCompraStockService::class)->actualizado($detalle);
    }

    public function deleted(DetalleCompra $detalle): void
    {
        app(DetalleCompraStockService::class)->eliminado($detalle);
    }
}

