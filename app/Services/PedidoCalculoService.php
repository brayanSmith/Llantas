<?php

namespace App\Services;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;

class PedidoCalculoService
{
    //obtener codigo del pedido de concatenando PED- con el id del pedido con ceros a la izquierda hasta completar 6 digitos
    public static function generarCodigoPedido(int $pedidoId): string
    {
        return 'PED-' . str_pad($pedidoId, 6, '0', STR_PAD_LEFT);
    }
    
    // Obtiene el valor unitario del producto según el tipo de precio
    public static function obtenerValorUnitario(Producto|array $producto, string $tipoPrecio): float
    {
        // Soportar tanto objetos Producto como arrays
        $valorDetal = is_array($producto) ? ($producto['valor_detal_producto'] ?? 0) : ($producto->valor_detal_producto ?? 0);
        $valorMayorista = is_array($producto) ? ($producto['valor_mayorista_producto'] ?? 0) : ($producto->valor_mayorista_producto ?? 0);
        $valorFerretero = is_array($producto) ? ($producto['valor_ferretero_producto'] ?? 0) : ($producto->valor_ferretero_producto ?? 0);

        if ($tipoPrecio === 'DETAL') {
            return $valorDetal;
        } elseif ($tipoPrecio === 'MAYORISTA') {
            return $valorMayorista;
        } elseif ($tipoPrecio === 'FERRETERO') {
            return $valorFerretero;
        }       
    }

    /**
     * Recalcula todos los valores del detalle del pedido según el tipo de precio seleccionado
     * 
     * @param array $detalles Array de detalles del pedido
     * @param string $tipoPrecio Tipo de precio (DETAL, FERRETERO, MAYORISTA)
     * @return array Detalles actualizados
     */
    public static function calcularDatosProducto(array $detalles, string $tipoPrecio): array
    {
        $detallesActualizados = [];
        
        foreach($detalles as $detalle){
            if (!isset($detalle['producto_id']) || !$detalle['producto_id']) {
                $detallesActualizados[] = $detalle;
                continue;
            }
            
            $producto = Producto::find($detalle['producto_id']);
            if (!$producto) {
                $detallesActualizados[] = $detalle;
                continue;
            }
            
            // Actualizar precio e IVA según el tipo de precio
            $detalle['precio_unitario'] = self::obtenerValorUnitario($producto, $tipoPrecio);
            $detalle['iva'] = $producto->iva_producto;
            
            // Recalcular subtotal
            $detalle['subtotal'] = self::calcularDetalles([
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'] ?? 0,
                'precio_unitario' => $detalle['precio_unitario'],
                'aplicar_iva' => $detalle['aplicar_iva'] ?? true,
                'iva' => $detalle['iva'],
            ]);
            
            $detallesActualizados[] = $detalle;
        }
        
        return $detallesActualizados;
    }
    
    /**
     * 🔹 Calcula datos del producto para el detalle del pedido
     */
    public static function obtenerDatosProducto(Producto $producto): array
    {
        return [            
            'iva' => $producto->iva_producto,
        ];
    }

    

     /**
     * 🔹 Calcula el total del detalle del pedido
     */

    public static function calcularDetalles(array $data): float
    {
        $productoId = $data['producto_id'] ?? null;
        $cantidad = (float) ($data['cantidad'] ?? 0);
        $precioUnitario = (float) ($data['precio_unitario'] ?? 0);
        $aplicarIva = $data['aplicar_iva'] ?? true;
        $iva = (float) ($data['iva'] ?? 0);

        $subtotal = $cantidad * $precioUnitario;
        
        // Si aplicar IVA es true, calcular con IVA
        if ($aplicarIva && $iva > 0) {
            $totalConIva = $subtotal * (1 + ($iva / 100));
            return round($totalConIva, 2);
        }
        
        // Si no aplica IVA, retornar subtotal sin IVA
        return round($subtotal, 2);
    }

    public static function calcularTotalesPedido(array $detalles, array $abonos, float $descuento, float $flete): array
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

    public static function calcularEstadoPago(float $saldo): string 
    {
        return (round($saldo, 4) <= 0.0001) ? 'SALDADO' : 'EN_CARTERA';
    }
    
    public static function calcularEstadoVencimiento(Pedido $pedido): string {
        {
            $saldoPendiente = $pedido->saldo_pendiente ?? 0;
            $fechaVencimiento = $pedido->fecha_vencimiento;
            if ($saldoPendiente <= 0) {
                return 'SALDADA';
            }
            if ($fechaVencimiento) {
                $hoy = \Carbon\Carbon::now()->startOfDay();
                $fechaVenc = \Carbon\Carbon::parse($fechaVencimiento)->startOfDay();
                if ($hoy->greaterThan($fechaVenc)) {
                    return 'VENCIDA';
                } else {
                    return 'PENDIENTE';
                }
            }
            return 'SIN_VENCIMIENTO';
        }
    }
}
