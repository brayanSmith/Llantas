<?php

namespace App\Services;

use App\Models\Traslado;
use Illuminate\Support\Facades\DB;

class TrasladoService
{
    //
    protected StockCalculoService $stockCalculoService;
    public function __construct(StockCalculoService $stockCalculoService)
    {
        $this->stockCalculoService = $stockCalculoService;
    }
        /**
        * ✅ CREA REGISTROS BASE (cuando se crea un traslado)
        */
    public function crearProductosBodega(Traslado $traslado): void
    {
        $productoId = $traslado->producto_id;
        
        // Crear registro en bodega donante si no existe
        $this->stockCalculoService->crearProductosBodega(
            $traslado->bodega_donante_id, 
            [$productoId]
        );
        
        // Crear registro en bodega destino si no existe
        $this->stockCalculoService->crearProductosBodega(
            $traslado->bodega_destino_id, 
            [$productoId]
        );
    }

    public function creado(Traslado $traslado): void
    {
        DB::transaction(function () use ($traslado) {
            // Recalcular bodega donante
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $traslado->producto_id,
                $traslado->bodega_donante_id
            );

            // Recalcular bodega destino
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $traslado->producto_id,
                $traslado->bodega_destino_id
            );
            
        });
    }
    public function actualizado(Traslado $traslado): void
    {
        DB::transaction(function () use ($traslado) {
            $bodegaDonanteAnterior = $traslado->getOriginal('bodega_donante_id');
            $bodegaDestinoAnterior = $traslado->getOriginal('bodega_destino_id');
            $productoId = $traslado->producto_id;

            // Recalcular bodega donante anterior
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $productoId,
                $bodegaDonanteAnterior
            );

            // Recalcular bodega destino anterior
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $productoId,
                $bodegaDestinoAnterior
            );

            // Recalcular bodega donante actual
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $productoId,
                $traslado->bodega_donante_id
            );

            // Recalcular bodega destino actual
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $productoId,
                $traslado->bodega_destino_id
            );
        });
    }
    public function eliminado(Traslado $traslado): void
    {
        DB::transaction(function () use ($traslado) {
            // Recalcular bodega donante
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $traslado->producto_id,
                $traslado->bodega_donante_id
            );

            // Recalcular bodega destino
            $this->stockCalculoService->recalcularStockPorProductoYBodega(
                $traslado->producto_id,
                $traslado->bodega_destino_id
            );
        });
    }
}