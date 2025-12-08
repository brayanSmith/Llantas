<?php

namespace App\Services;

use App\Models\DetalleCompra;
use App\Models\StockBodega;
use Illuminate\Support\Facades\DB;

class DetalleCompraStockService
{
    protected StockCalculoService $stockCalculoService;

    public function __construct(StockCalculoService $stockCalculoService)
    {
        $this->stockCalculoService = $stockCalculoService;
    }

    public function creado(DetalleCompra $detalle): void
    {
        if ($detalle->compra->estado !== 'FACTURADO') {
            return;
        }

        DB::transaction(function () use ($detalle) {

            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $detalle->item_id,
                $detalle->compra->bodega_id
            );

        });
    }

    public function actualizado(DetalleCompra $detalle): void
    {
        if ($detalle->compra->estado !== 'FACTURADO') {
            return;
        }

        DB::transaction(function () use ($detalle) {

            $productoAnterior = $detalle->getOriginal('item_id');
            $productoActual   = $detalle->item_id;

            $bodegaAnterior   = $detalle->compra->getOriginal('bodega_id');
            $bodegaActual     = $detalle->compra->bodega_id;

            // ✅ Recalcular producto anterior
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $productoAnterior,
                $bodegaAnterior
            );

            // ✅ Recalcular producto nuevo
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $productoActual,
                $bodegaActual
            );
        });
    }

    public function eliminado(DetalleCompra $detalle): void
    {
        if ($detalle->compra->estado !== 'FACTURADO') {
            return;
        }

        DB::transaction(function () use ($detalle) {

            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $detalle->item_id,
                $detalle->compra->bodega_id
            );

        });
    }

    
}
