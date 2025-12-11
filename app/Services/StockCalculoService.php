<?php

namespace App\Services;

use App\Models\DetalleCompra;
use App\Models\DetallePedido;
use App\Models\StockBodega;
use App\Models\Traslado;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;


class StockCalculoService
{
    /**
     * Calcula el total de entradas facturadas de un producto en una bodega específica
     * 
     * @param int $productoId ID del producto
     * @param int $bodegaId ID de la bodega
     * @param int|null $excluirCompraId ID de compra a excluir del cálculo (útil al eliminar)
     * @return float Total de entradas
     */
    public function calcularEntradasFacturadas(int $productoId, int $bodegaId, ?int $excluirCompraId = null): float
    {         
        // Obtener el stock inicial del producto en la bodega específica
        $producto = Producto::where('id', $productoId)->first();
        $totalInicial = ($producto && $producto->bodega_id === $bodegaId) 
            ? ($producto->stock_inicial ?? 0) 
            : 0;

        // Suma las cantidades de DetalleCompra 
        $totalCompras = DetalleCompra::where('item_id', $productoId)
            ->whereHas('compra', function ($q) use ($bodegaId, $excluirCompraId) {
                $q->where('bodega_id', $bodegaId)
                  ->where('estado', 'FACTURADO')
                  ->where('item_compra', 'PRODUCTO');
                
                if ($excluirCompraId) {
                    $q->where('id', '!=', $excluirCompraId);
                }
            })
            ->sum('cantidad');
        
        //resta las cantidades de Traslados en la que la bodega donante es la bodegaId    
        $totalDonaciones = Traslado::where('producto_id', $productoId)
            ->where('bodega_donante_id', $bodegaId)
            ->sum('cantidad');

        $totalTraslados = Traslado::where('producto_id', $productoId)
            ->where('bodega_destino_id', $bodegaId)
            ->sum('cantidad');

        // Retorna el total calculado
        return  $totalInicial + $totalCompras - $totalDonaciones + $totalTraslados;

    }

    public function calcularSalidasFacturadas(int $productoId, int $bodegaId, ?int $excluirVentaId = null): float
    {
      $totalPedidos = DetallePedido::where('producto_id', $productoId)
            ->whereHas('pedido', function ($q) use ($bodegaId, $excluirVentaId) {
                $q->where('bodega_id', $bodegaId)
                  ->whereIn('estado', ['PENDIENTE','FACTURADO','EN_RUTA' ,'ENTREGADO', 'PENDIENTE']);

                if ($excluirVentaId) {
                    $q->where('id', '!=', $excluirVentaId);
                }
            })
            ->sum('cantidad');

        // Retorna el total calculado
        return $totalPedidos;

    }

    /**
     * Recalcula y actualiza el stock de entradas en StockBodega
     * 
     * @param int $productoId ID del producto
     * @param int $bodegaId ID de la bodega
     * @param int|null $excluirCompraId ID de compra a excluir del cálculo
     * @return void
     */
    public function recalcularStockPorProductoYBodega(int $productoId, int $bodegaId, ?int $excluirCompraId = null, ?int $excluirVentaId = null): void
    {
        $totalCompras = $this->calcularEntradasFacturadas($productoId, $bodegaId, $excluirCompraId);
        $totalPedidos = $this->calcularSalidasFacturadas($productoId, $bodegaId, $excluirVentaId);
        $existencias = $totalCompras - $totalPedidos;

        $stock = StockBodega::lockForUpdate()
            ->where('producto_id', $productoId)
            ->where('bodega_id', $bodegaId)
            ->first();

        if ($stock) {
            $stock->entradas = $totalCompras;
            $stock->salidas = $totalPedidos;
            $stock->stock = $existencias;
            $stock->save(); // Guardar sin disparar eventos
        } else {
            StockBodega::create([
                'producto_id' => $productoId,
                'bodega_id'   => $bodegaId,
                'entradas'    => $totalCompras,
                'salidas'     => $totalPedidos,
                'stock'      => $existencias,
            ]);
        }
    }

    /**
     * Crea registros en StockBodega para productos que aún no existen en una bodega específica
     * 
     * @param int $bodegaId ID de la bodega
     * @param array $productosIds Array de IDs de productos
     * @return void
     */
    public function crearProductosBodega(int $bodegaId, array $productosIds): void
    {
        DB::transaction(function () use ($bodegaId, $productosIds) {
            foreach ($productosIds as $productoId) {
                $registroStock = StockBodega::where('producto_id', $productoId)
                    ->where('bodega_id', $bodegaId)
                    ->lockForUpdate()
                    ->first();

                if ($registroStock === null) {
                    StockBodega::create([
                        'producto_id' => $productoId,
                        'bodega_id'   => $bodegaId,
                    ]);
                }
            }
        });
    }
}
