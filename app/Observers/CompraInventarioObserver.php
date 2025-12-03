<?php

namespace App\Observers;

use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class CompraInventarioObserver
{
    public function updated(Compra $compra): void
    {
        // Estados que AUMENTAN inventario (aumentan entradas)
        $estadosAumentar = ['FACTURADO', 'COMPLETADO'];
        
        // Estados que NO afectan inventario (no aumentan entradas)
        $estadosNoAfectan = ['PENDIENTE', 'ANULADO'];
        
        $estadoAnterior = $compra->getOriginal('estado');
        $estadoActual = $compra->estado;

        // Solo procesar cambios de estado
        if ($estadoAnterior === $estadoActual) return;

        // Si pasa de estado "no afectar" a estado "aumentar" -> aumentar entradas
        if (in_array($estadoAnterior, $estadosNoAfectan) && 
            in_array($estadoActual, $estadosAumentar)) {
            $this->ajustarInventario($compra, 'aumentar');
        }

        // Si pasa de estado "aumentar" a estado "no afectar" -> reducir entradas
        if (in_array($estadoAnterior, $estadosAumentar) && 
            in_array($estadoActual, $estadosNoAfectan)) {
            $this->ajustarInventario($compra, 'reducir');
        }
    }
    private function ajustarInventario(Compra $compra, string $accion): void
    {
        DB::transaction(function () use ($compra, $accion) {
            foreach ($compra->detallesCompra as $detalle) {
                $cantidad = (float) ($detalle->cantidad ?? 0);
                if ($cantidad <= 0) continue;

                $producto = Producto::lockForUpdate()->find($detalle->item_id);
                if (!$producto) continue;

                if ($accion === 'aumentar') {
                    // Aumentar entradas
                    $producto->entradas = ($producto->entradas ?? 0) + $cantidad;
                } else { // reducir
                    // Reducir entradas
                    $producto->entradas = max(0, ($producto->entradas ?? 0) - $cantidad);
                }

                $producto->save();
            }

            // Marcar si las entradas fueron aplicadas o revertidas
            $compra->updateQuietly([
                'entradas_aplicadas' => ($accion === 'aumentar') ? true : false,
            ]);
        });
    }
}