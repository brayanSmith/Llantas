<?php

namespace App\Services;

use Carbon\Carbon;

class VencimientoService
{
    /**
     * Calcula la fecha de vencimiento sumando días de plazo a una fecha de inicio.
     */
    public static function calcularFechaVencimiento(string $fechaInicio, int $diasPlazo): ?string
    {
        if (empty($fechaInicio)) {
            return null;
        }

        try {
            return Carbon::parse($fechaInicio)->addDays($diasPlazo)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Calcula los días restantes (con signo) entre hoy y la fecha de vencimiento.
     */
    public static function diasRestantes(string $fechaVencimiento): ?int
    {
        if (empty($fechaVencimiento)) {
            return null;
        }

        try {
            return Carbon::today()->diffInDays(
                Carbon::parse($fechaVencimiento)->startOfDay(),
                false
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function estadoVencimiento(object $modelo): ?string
    {
        $dias = self::diasRestantes($modelo->fecha_vencimiento);

        if ($dias === null) return null;

        if ($dias < 0) {
            return 'VENCIDO';
        } elseif ($dias === 0) {
            return 'AL_DIA';
        } elseif ($dias > 0) {
            return 'AL_DIA';
        }

        return null;
    }

    /**
     * Retorna el estado del vencimiento:
     * - vencido
     * - hoy
     * - proximo (<=7 días)
     * - normal
     */
    public static function estado(string $fechaVencimiento): ?string
    {
        $dias = self::diasRestantes($fechaVencimiento);

        if ($dias === null) return null;

        return match (true) {
            $dias < 0  => 'vencido',
            $dias === 0 => 'hoy',
            $dias <= 7  => 'proximo',
            default     => 'normal',
        };
    }

    /**
     * Mensaje visible en Filament, igual a tu lógica original.
     */
    public static function mensaje(string $fechaVencimiento): string
    {
        $dias = self::diasRestantes($fechaVencimiento);
        $estado = self::estado($fechaVencimiento);

        if ($dias === null || $estado === null) {
            return '';
        }

        return match ($estado) {
            'vencido' => "Vencido hace " . abs($dias) . " día" . (abs($dias) === 1 ? '' : 's'),
            'hoy'     => "Vence hoy",
            'proximo' => "Quedan {$dias} día" . ($dias === 1 ? '' : 's') . " para vencerse",
            'normal'  => "Quedan {$dias} días para vencerse",
            default   => '',
        };
    }

    /**
     * Estilos CSS iguales que en tu Resource original.
     */
    public static function estilo(string $fechaVencimiento): string
    {
        $estado = self::estado($fechaVencimiento);

        return match ($estado) {
            'vencido' => 'text-sm bg-red-600 text-red-50 mb-2 p-2 rounded',
            'hoy'     => 'text-sm bg-yellow-600 text-yellow-50 mb-2 p-2 rounded',
            'proximo' => 'text-sm bg-yellow-600 text-yellow-50 mb-2 p-2 rounded',
            'normal'  => 'text-sm bg-green-600 text-green-50 mb-2 p-2 rounded',
            default   => 'text-sm mb-2',
        };
    }
}
