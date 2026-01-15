<?php

namespace App\Services\Pedido;

use App\Models\Abono;
use App\Models\Pedido;
use Carbon\Carbon;
use App\Services\Pedido\PedidoCalculoService;

class PedidoEstados
{
    public static function setUltimaFechaAbono(Pedido $pedido): void
    {
        $ultimoAbono = Abono::where('pedido_id', $pedido->id)
            ->orderBy('fecha', 'desc')
            ->first();

        $pedido->fecha_ultimo_abono = $ultimoAbono?->fecha;
        $pedido->saveQuietly();
    }

    public static function setDiasPlazoCartera(Pedido $pedido): void
    {
        self::setUltimaFechaAbono($pedido);
        $pedido->refresh(); // Recargar datos actualizados
        $plazo = 30; // Plazo estándar de 30 días

        $fechaActual = now();

        if ($pedido->fecha_ultimo_abono) {
            // Calcular desde último abono + 30 días
            $fechaVencimientoAbono = Carbon::parse($pedido->fecha_ultimo_abono)->addDays($plazo);
        } else {
            // Usar fecha de vencimiento del pedido
            $fechaVencimientoAbono = Carbon::parse($pedido->fecha);
        }

        // Calcular días restantes (puede ser negativo si ya venció)
        $diasRestantes = $fechaActual->diffInDays($fechaVencimientoAbono, false);
        $pedido->dias_plazo_cartera = max(0, $diasRestantes);
        $pedido->saveQuietly();
    }

    public static function setEstadoCartera(Pedido $pedido): void
    {
        self::setDiasPlazoCartera($pedido);
        $pedido->refresh(); // Recargar datos actualizados

        if ($pedido->saldo_pendiente <= 0) {
            $pedido->estado_cartera = 'CARTERA_PAGADA';
        } elseif ($pedido->dias_plazo_cartera > 0) {
            $pedido->estado_cartera = 'CARTERA_AL_DIA';
        } else {
            $pedido->estado_cartera = 'CARTERA_VENCIDA';
        }

        $pedido->saveQuietly();
    }

    public static function actualizarEstadoCartera(Pedido $pedido): void
    {
        $estado = $pedido->estado;
        if(in_array($estado, ['PENDIENTE', 'ANULADO'])) {
            $pedido->estado_cartera = 'NO_APLICA';
            $pedido->saveQuietly();
        }else {
        self::setUltimaFechaAbono($pedido);
        self::setDiasPlazoCartera($pedido);
        self::setEstadoCartera($pedido);
        PedidoCalculoService::setPedidosEnCarteraTotales($pedido->cliente, $pedido->cliente_id);
        }
    }
}
