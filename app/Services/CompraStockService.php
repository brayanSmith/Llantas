<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use App\Models\StockBodega;
use App\Models\DetalleCompra;


class CompraStockService
{
    protected StockCalculoService $stockCalculoService;

    public function __construct(StockCalculoService $stockCalculoService)
    {
        $this->stockCalculoService = $stockCalculoService;
    }

    /**
     * Crea registros en StockBodega para los productos de la compra si no existen
     * 
     * @param Compra $compra
     * @return void
     */
    public function crearProductosBodega(Compra $compra): void
    {
        $productosIds = $compra->detallesCompra->pluck('item_id')->toArray();
        $this->stockCalculoService->crearProductosBodega($compra->bodega_id, $productosIds);
    }

    /**
     * ✅ CREA REGISTROS BASE (cuando se crea una compra)
     */
    public function creado(Compra $compra): void
    {
        DB::transaction(function () use ($compra) {

            foreach ($compra->detallesCompra as $detalle) {

                StockBodega::firstOrCreate([
                    'producto_id' => $detalle->item_id,
                    'bodega_id'   => $compra->bodega_id,
                ], [
                    'entradas' => 0,
                ]);

                // ✅ Si ya entra facturada → recalcula
                if ($compra->estado === 'FACTURADO') {
                    $this->stockCalculoService->recalcularStockPorProductoYBodega(
                        $detalle->item_id,
                        $compra->bodega_id
                    );
                }
            }

        });
    }

    /**
     * ✅ ACTUALIZA STOCK CUANDO:
     * - CAMBIA BODEGA
     * - CAMBIA ESTADO
     */
    public function actualizado(Compra $compra): void
    {
        DB::transaction(function () use ($compra) {

            $bodegaAnterior = $compra->getOriginal('bodega_id');
            $bodegaActual   = $compra->bodega_id;

            $estadoAnterior = $compra->getOriginal('estado');
            $estadoActual   = $compra->estado;

            foreach ($compra->detallesCompra as $detalle) {

                $productoId = $detalle->item_id;

                // ✅ Recalcular bodega actual
                $this->stockCalculoService->recalcularStockPorProductoYBodega(
                    $productoId,
                    $bodegaActual
                );

                // ✅ Si cambió de bodega → recalcular anterior
                if ($bodegaAnterior && $bodegaAnterior !== $bodegaActual) {
                    $this->stockCalculoService->recalcularStockPorProductoYBodega(
                        $productoId,
                        $bodegaAnterior
                    );
                }

                // ✅ Si cambió el estado → recalcular bodega actual
                if ($estadoAnterior !== $estadoActual) {
                    $this->stockCalculoService->recalcularStockPorProductoYBodega(
                        $productoId,
                        $bodegaActual
                    );
                }
            }

        });
    }

    /**
     * ✅ ELIMINA EL EFECTO DE LA COMPRA EN INVENTARIO
     */
    public function eliminado(Compra $compra): void
    {
        DB::transaction(function () use ($compra) {

            // ✅ Cargar detalles antes de que se eliminen
            $detalles = $compra->detallesCompra()->get();
            $bodegaId = $compra->bodega_id;
            $compraId = $compra->id;

            foreach ($detalles as $detalle) {

                // ✅ Recalcular excluyendo esta compra
                $this->stockCalculoService->recalcularStockPorProductoYBodega(
                    $detalle->item_id,
                    $bodegaId,
                    $compraId
                );
            }

        });
    }
}