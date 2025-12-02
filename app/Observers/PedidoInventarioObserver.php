<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class PedidoInventarioObserver
{
    public function updated(Pedido $pedido): void
    {
        // Estados que DESCUENTAN inventario (aumentan salidas)
        $estadosDescontar = ['FACTURADO', 'EN_RUTA', 'ENTREGADO'];
        
        // Estados que NO afectan inventario (no aumentan salidas)
        $estadosContar = ['PENDIENTE', 'ANULADO', 'DEVUELTO'];
        
        $estadoAnterior = $pedido->getOriginal('estado');
        $estadoActual = $pedido->estado;

        // Solo procesar cambios de estado
        if ($estadoAnterior === $estadoActual) return;

        // Si pasa de estado "contar" a estado "descontar" -> aumentar salidas
        if (in_array($estadoAnterior, $estadosContar) && 
            in_array($estadoActual, $estadosDescontar)) {
            $this->ajustarInventario($pedido, 'descontar');
        }

        // Si pasa de estado "descontar" a estado "contar" -> reducir salidas
        if (in_array($estadoAnterior, $estadosDescontar) && 
            in_array($estadoActual, $estadosContar)) {
            $this->ajustarInventario($pedido, 'devolver');
        }
    }

    private function ajustarInventario(Pedido $pedido, string $accion): void
    {
        DB::transaction(function () use ($pedido, $accion) {
            foreach ($pedido->detalles as $detalle) {
                $cantidad = (float) ($detalle->cantidad ?? 0);
                if ($cantidad <= 0) continue;

                $producto = Producto::lockForUpdate()->find($detalle->producto_id);
                if (!$producto) continue;

                if ($accion === 'descontar') {
                    // Aumentar salidas
                    $producto->salidas = ($producto->salidas ?? 0) + $cantidad;
                } else { // devolver
                    // Reducir salidas
                    $producto->salidas = max(0, ($producto->salidas ?? 0) - $cantidad);
                }

                $producto->save();
            }

            // Marcar si las salidas fueron aplicadas o revertidas
            $pedido->updateQuietly([
                'stock_retirado' => $accion === 'descontar'
            ]);
        });
    }
}