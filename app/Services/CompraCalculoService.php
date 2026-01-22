<?php

namespace App\Services;

use App\Models\DetalleCompra;

class CompraCalculoService
{
    //Calcula el subtotal por item
    public static function calcularDetalles(array $item): array
    {
        $itemId = $item['item_id'] ?? null;
        $descripcion = $item['descripcion_item'] ?? '';
        $cantidad = $item['cantidad'] ?? 0;
        $precio_unitario = $item['precio_unitario'] ?? 0;
        $iva = $item['iva'] ?? 0;

        $subtotal = $cantidad * $precio_unitario * (1 + ($iva / 100));
        $precioConIva = $subtotal;
        return [
            'subtotal' => $subtotal,
            'precio_con_iva' => $precioConIva
        ];
    }

    public static function calcularAbonos(array $abonos): float
    {
        return collect($abonos)->sum(function($item){
            $monto = $item['monto_abono_compra'] ?? $item['monto'] ?? 0;
            return (float) $monto;
        });
    }

    //Calcula subtotal, abono, total a pagar y saldo pendiente
    public static function calcular(array $detalles, array $abonos, float $descuento)
    {
        $subtotal = collect($detalles)->sum(function($item){
            $resultado = self::calcularDetalles($item);
            return $resultado['subtotal'];
        });
        $totalAbonos = collect($abonos)->sum(function($item){
            $monto = $item['monto_abono_compra'] ?? $item['monto'] ?? 0;
            return (float) $monto;
        });

        $total_a_pagar = $subtotal - $descuento;
        $saldo = $total_a_pagar - $totalAbonos;

        return [
            'subtotal' => $subtotal,
            'abono' => $totalAbonos,
            'total_a_pagar' => $total_a_pagar,
            'saldo_pendiente' => $saldo,
        ];
    }

    public static function calcularEstadoPago(float $saldo): string
    {
        return (round($saldo, 4) <= 0.0001) ? 'SALDADO' : 'EN_CARTERA';
    }
    //esta funcion determina si el detalle de compra es de tipo producto o gasto
    public static function esProductoGasto(DetalleCompra $detalleCompra): string
    {
        if($detalleCompra->compra->item_compra === 'PRODUCTO') {
            return 'PRODUCTO';
        } else {
            return 'GASTO';
        }
    }

}
