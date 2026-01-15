<?php

namespace App\Services\Pedido;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\StockBodega;
use Illuminate\Support\Facades\DB;
use App\Services\StockCalculoService;

class PedidoStockService
{
    protected StockCalculoService $stockCalculoService;

    public function __construct(StockCalculoService $stockCalculoService)
    {
        $this->stockCalculoService = $stockCalculoService;
    }
    public function creado(Pedido $pedido): void
    {
        DB::transaction(function () use ($pedido) {

            foreach ($pedido->detalles as $detalle) {

                $this->stockCalculoService->recalcularStockPorProductoYBodega(
                    $detalle->producto_id,
                    $pedido->bodega_id
                );
            }
        });
    }
    public function actualizado(Pedido $pedido): void
    {
        DB::transaction(function () use ($pedido) {

                $bodegaAnterior = $pedido->getOriginal('bodega_id');
                $bodegaActual   = $pedido->bodega_id;

                $estadoAnterior = $pedido->getOriginal('estado');
                $estadoActual   = $pedido->estado;

                foreach ($pedido->detalles as $detalle) {

                    $productoId = $detalle->producto_id;

                    //Recalcular Bodega Actual
                    $this->stockCalculoService->recalcularStockPorProductoYBodega(
                        $productoId,
                        $bodegaActual
                    );
                    //Recalcular Bodega Anterior si cambió
                    if ($bodegaAnterior && $bodegaAnterior !== $bodegaActual) {
                        $this->stockCalculoService->recalcularStockPorProductoYBodega(
                            $productoId,
                            $bodegaAnterior
                        );
                    }
                    //Recalcular si cambió el estado entre los que afectan stock
                    if($estadoAnterior && $estadoAnterior !== $estadoActual){
                        $this->stockCalculoService->recalcularStockPorProductoYBodega(
                            $productoId,
                            $bodegaActual
                        );
                    }
            }
        });
    }

    public function eliminado(Pedido $pedido): void
    {
        DB::transaction(function () use ($pedido) {

            // Cargar detalles antes de que se eliminen
            $detalles = $pedido->detalles()->get();
            $bodegaId = $pedido->bodega_id;
            $pedidoId = $pedido->id;

            foreach ($detalles as $detalle) {

                // Recalcular excluyendo este pedido
                $this->stockCalculoService->recalcularStockPorProductoYBodega(
                    $detalle->producto_id,
                    $bodegaId,
                    $pedidoId
                );
            }
        });
    }

}
