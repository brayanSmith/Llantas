<?php

namespace App\Services\Pedido;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Abono;
use App\Models\Cliente;

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
     * Actualiza el vendedor de todos los abonos de un pedido
     */
    public static function actualizarVendedorAbonos(Pedido $pedido): int
    {
        if (!$pedido->user_id) {
            return 0;
        }

        // Actualizar todos los abonos del pedido con el vendedor del pedido
        return Abono::where('pedido_id', $pedido->id)
            ->update(['vendedor_id' => $pedido->user_id]);
    }


     /**
     * 🔹 Calcula el total del detalle del pedido
     */

    public static function calcularDetalles(array $data): array
    {
       /* $productoId = $data['producto_id'] ?? null;
        $cantidad = (float) ($data['cantidad'] ?? 0);
        $precioUnitario = (float) ($data['precio_unitario'] ?? 0);

        $subtotal = $cantidad * $precioUnitario;

        // Si aplicar IVA es true, calcular con IVA

            $totalConIva = $subtotal * (1 + ($iva / 100));
            $precioConIva = $precioUnitario * (1 + ($iva / 100));
            $subTotal = round($totalConIva, 2);
        }else{
            // Si no, retornar subtotal sin IVA
            $precioConIva = $precioUnitario;
            $subTotal = round($subtotal, 2);
        }
        return [
            'subtotal' => $subTotal,
            'precio_con_iva' => $precioConIva
        ];*/
    }

    public static function calcularTotalesPedido(array $detalles, array $abonos, float $descuento, float $flete): array
    {
        $subtotal = collect($detalles)->sum(function($item){
            $resultado = self::calcularDetalles($item);
            return $resultado['subtotal'];
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
        return (round($saldo, 2) <= 0) ? 'SALDADO' : 'EN_CARTERA';
    }

    public static function setPedidosEnCarteraTotales(Cliente $cliente, string $clienteId): void
    {
        $totalPedidos = Pedido::where('cliente_id', $clienteId)
            ->where('estado_pago', 'EN_CARTERA')
            ->whereIn('estado', ['FACTURADO', 'EN_RUTA', 'ENTREGADO'])
            ->count();

        $saldoTotal = Pedido::where('cliente_id', $clienteId)
            ->where('estado_pago', 'EN_CARTERA')
            ->whereIn('estado', ['FACTURADO', 'EN_RUTA', 'ENTREGADO'])
            ->sum('saldo_pendiente');

        $saldoVencido = Pedido::where('cliente_id', $clienteId)
            ->where('estado_pago', 'EN_CARTERA')
            ->whereIn('estado', ['FACTURADO', 'EN_RUTA', 'ENTREGADO'])
            ->where('estado_vencimiento', 'VENCIDO')
            ->sum('saldo_pendiente');

        $cliente->cuenta_total_pedidos_en_cartera = $totalPedidos;
        $cliente->saldo_total_pedidos_en_cartera = $saldoTotal;
        $cliente->saldo_total_pedidos_vencidos = $saldoVencido;
        $cliente->saveQuietly();
    }

}
