<?php

namespace App\Filament\Resources\Pedidos\Pages\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait InteractsWithDetallePedidosTab
{
    protected function applyDetallePedidosTabQuery(Builder $query, bool $includeResumenCartera = false): Builder
    {
        return $query
            ->join('detalle_pedidos', 'pedidos.id', '=', 'detalle_pedidos.pedido_id')
            ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
            ->select($this->getDetallePedidosTabSelects($includeResumenCartera))
            ->with(['cliente']);
    }

    protected function getDetallePedidosTabSelects(bool $includeResumenCartera = false): array
    {
        $selects = [
            'detalle_pedidos.id as id',
            'pedidos.id as pedido_base_id',
            'pedidos.fecha',
            'pedidos.cliente_id',
            'detalle_pedidos.producto_id',
            'detalle_pedidos.cantidad',
            'detalle_pedidos.precio_unitario',
            'detalle_pedidos.costo_unitario',
            'detalle_pedidos.costo_total',
            'detalle_pedidos.ganancia_total',
            'detalle_pedidos.subtotal',
            'productos.concatenar_codigo_nombre as producto_nombre',
            DB::raw('(SELECT COALESCE(SUM(stock_bodegas.stock), 0) FROM stock_bodegas WHERE stock_bodegas.producto_id = detalle_pedidos.producto_id) as stock_total'),
            DB::raw("(SELECT COALESCE(SUM(dp.cantidad), 0)
                FROM detalle_pedidos dp
                INNER JOIN pedidos p ON p.id = dp.pedido_id
                WHERE dp.producto_id = detalle_pedidos.producto_id
                  AND p.estado_pago = 'EN_CARTERA'
                  AND p.tipo_precio = 'MAYORISTA') as consignacion"),
            DB::raw("(SELECT COALESCE(SUM(dp.cantidad), 0)
                FROM detalle_pedidos dp
                INNER JOIN pedidos p ON p.id = dp.pedido_id
                WHERE dp.producto_id = detalle_pedidos.producto_id
                  AND p.estado_pago = 'EN_CARTERA'
                  AND p.tipo_precio = 'DETAL') as en_cartera"),
        ];

        return $selects;
    }
}
