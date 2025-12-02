<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoObserver
{
    //
    public function saved(Pedido $pedido) 
    {
        $pedido->recalcularTotales(); //ajustar iva si aplica
    }

    public function updated(Pedido $pedido): void
    {
        // 1) Asegurarse de que totales están actualizados
        if (method_exists($pedido, 'recalcularTotales')) {
            // recalcula en memoria y persiste si tu método lo hace
            $pedido->recalcularTotales();
            // refrescar para tener valores persistidos en la BD
            $pedido->refresh();
        }

        // 2) Controlar estado_pago según total a pagar
        // calcular total_a_pagar fiable (usa columna total_a_pagar si existe)
        $totalAPagar = $pedido->total_a_pagar ?? (($pedido->subtotal ?? 0) - ($pedido->abono ?? 0) - ($pedido->descuento ?? 0));
        $epsilon = 0.0001;

        // si está esencialmente en 0 -> marcar SALDADO
        if (round((float) $totalAPagar, 4) <= $epsilon) {
            if (($pedido->estado_pago ?? '') !== 'SALDADO') {
                // updateQuietly evita re-disparar observers recursivamente
                $pedido->updateQuietly(['estado_pago' => 'SALDADO']);
                Log::info("Pedido {$pedido->id} marcado SALDADO automáticamente (total a pagar = 0).");
            }
        } else {
            // si vuelve a deber dinero y está SALDADO, revertir a EN_CARTERA
            if (($pedido->estado_pago ?? '') === 'SALDADO') {
                $pedido->updateQuietly(['estado_pago' => 'EN_CARTERA']);
                Log::info("Pedido {$pedido->id} revertido a EN_CARTERA (total a pagar > 0).");
            }
        }
    }
}
