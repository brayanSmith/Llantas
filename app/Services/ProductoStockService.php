<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\StockBodega;
use Illuminate\Support\Facades\DB;

class ProductoStockService
{
    protected StockCalculoService $stockCalculoService;

    public function __construct(StockCalculoService $stockCalculoService)
    {
        $this->stockCalculoService = $stockCalculoService;
    }

    public function recalcularStockPorProducto(int $productoId): void
    {
        DB::transaction(function () use ($productoId) {

            $producto = Producto::findOrFail($productoId);

            $bodegasIds = StockBodega::where('producto_id', $productoId)
                ->distinct()
                ->pluck('bodega_id');

            foreach ($bodegasIds as $bodegaId) {
                $this->stockCalculoService->recalcularStockPorProductoYBodega(
                    $productoId,
                    $bodegaId
                );
            }
        });
    }
}