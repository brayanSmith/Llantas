<?php

namespace App\Services\Pedido;

use App\Models\Abono;
use App\Models\Pedido;
use Carbon\Carbon;
use App\Services\Pedido\PedidoCalculoService;
use App\Services\VencimientoService;

class PedidoEstados
{
    public static function setEstadoCartera(Pedido $pedido): void
    {
        // Calcular días plazo cartera
        $pedido->dias_plazo_cartera = VencimientoService::diasRestantes($pedido->fecha_vencimiento) ?? 0;
        $pedido->saveQuietly();
        $pedido->refresh(); // Recargar datos actualizados
        // Calcular estado cartera
        $pedido->estado_vencimiento = VencimientoService::estadoVencimiento($pedido);
        $pedido->saveQuietly();
    }

    public static function actualizarEstadoCartera(Pedido $pedido): void
    {
        self::setEstadoCartera($pedido);
        PedidoCalculoService::setPedidosEnCarteraTotales($pedido->cliente, $pedido->cliente_id);
    }

}
