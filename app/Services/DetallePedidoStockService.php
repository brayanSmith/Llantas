<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\StockBodega;
use Illuminate\Support\Facades\DB;

class DetallePedidoStockService
{
    protected StockCalculoService $stockCalculoService;

    public function __construct(StockCalculoService $stockCalculoService)
    {
        $this->stockCalculoService = $stockCalculoService;
    }
    public function creado(DetallePedido $detallePedido): void
    {
        if (!in_array($detallePedido->estado, ['ENTREGADO', 'FACTURADO', 'EN_RUTA', 'PENDIENTE'])) {
            return;
        }

        DB::transaction(function () use ($detallePedido) {            

                $this->stockCalculoService->recalcularStockPorProductoYBodega(
                    $detallePedido->producto_id,
                    $detallePedido->pedido->bodega_id
                );
            
        });
    }
    public function actualizado(DetallePedido $detallePedido): void
    {        

        DB::transaction(function () use ($detallePedido) {
            $productoAnterior = $detallePedido->getOriginal('producto_id');
            $productoActual   = $detallePedido->producto_id;

            $bodegaAnterior   = $detallePedido->pedido->getOriginal('bodega_id');
            $bodegaActual     = $detallePedido->pedido->bodega_id;

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
    public function eliminado(DetallePedido $detallePedido): void
    {
        DB::transaction(function () use ($detallePedido) {            

                $this->stockCalculoService->recalcularStockPorProductoYBodega(
                    $detallePedido->producto_id,
                    $detallePedido->pedido->bodega_id
                );
            
        });
    }
}

