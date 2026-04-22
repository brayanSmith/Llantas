<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Bodega;
use App\Models\StockBodega;
use Illuminate\Support\Facades\DB;

class ProductoStockService
{
    protected StockCalculoService $stockCalculoService;

    public function __construct(StockCalculoService $stockCalculoService)
    {
        $this->stockCalculoService = $stockCalculoService;
    }
    // Crea registros en StockBodega para el producto en todas las bodegas activas
    public function crearProductosBodega(Producto $producto): void
    {
        $productosIds = [$producto->id];

        // Obtener todas las bodegas activas
        $bodegasIds = Bodega::pluck('id');

        if($producto->inventariable === false){
            return; // No crear stock para productos no inventariables
        }
        // Crear stock en cada bodega
        foreach ($bodegasIds as $bodegaId) {
            $this->stockCalculoService->crearProductosBodega($bodegaId, $productosIds);
        }
    }

    // Recalcula el stock de un producto en todas sus bodegas
    public function recalcularStockTodasBodegas(int $productoId): void
    {
        $bodegasIds = StockBodega::where('producto_id', $productoId)
            ->pluck('bodega_id');

        DB::transaction(function () use ($productoId, $bodegasIds) {
            foreach ($bodegasIds as $bodegaId) {
                $this->stockCalculoService->recalcularStockPorProductoYBodega(
                    $productoId,
                    $bodegaId
                );
            }
        });
    }
}
