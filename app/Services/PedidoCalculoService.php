<?php

namespace App\Services;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;

class PedidoCalculoService
{
    /**
     * 🔹 Calcula datos del producto para el detalle del pedido
     */
    public static function calcularDatosProducto(Producto $producto): array
    {
        return [
            'precio_unitario' => $producto->precio_venta,
            'iva' => $producto->iva_producto,
        ];
    }

     /**
     * 🔹 Calcula el total del detalle del pedido
     */

    public static function calcularDetalles(array $data): float
    {
        $productoId = $data['producto_id'] ?? null;
        $cantidad = $data['cantidad'] ?? 0;
        $precioUnitario = $data['precio_unitario'] ?? 0;
        $aplicarIva = $data['aplicar_iva'] ?? false;       
        $iva = $data['iva'] ?? 0;

        $subtotal = $cantidad * $precioUnitario;
        $totalIva = $subtotal * (($iva / 100) + 1);

        if ($aplicarIva === 0) {
            return round($subtotal, 2);
        }else {
            return round($totalIva, 2);
        }        
    }
    public static function calcularTotalesCompra(array $detalles, array $abonos, float $descuento, float $flete): array
    {
        $subtotal = collect($detalles)->sum(function($item){
            return self::calcularDetalles($item);
        });

        $totalAbonos = collect($abonos)->sum(function($item){
            $monto = $item['monto_abono_pedido'] ?? $item['monto'] ?? 0;
            return (float) $monto;
        });

        $total_a_pagar = $subtotal + $flete - $descuento;
        $saldo = $total_a_pagar - $totalAbonos;

        return [
            'subtotal' => $subtotal,
            'abono' => $totalAbonos,
            'total_a_pagar' => $total_a_pagar,
            'saldo_pendiente' => $saldo,
        ];
    }
    public static function calcularEstadoPago(float $saldo): string {
        return (round($saldo, 4) <= 0.0001) ? 'SALDADO' : 'EN_CARTERA';
    }
    public static function calcularEstadoVencimiento(Pedido $pedido): string {
        $hoy = now()->startOfDay();
        $fechaVencimiento = $pedido->fecha_vencimiento ? \Carbon\Carbon::parse($pedido->fecha_vencimiento)->startOfDay() : null;

        if (!$fechaVencimiento) {
            return 'SIN_VENCIMIENTO';
        }

        if ($hoy->gt($fechaVencimiento)) {
            return 'VENCIDA';
        }

        $diasRestantes = $hoy->diffInDays($fechaVencimiento, false);

        if ($diasRestantes <= 5) {
            return 'POR_VENCER';
        }

        return 'AL_DIA';
    }

}
