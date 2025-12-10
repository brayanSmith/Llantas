<?php

namespace App\Services;

use Carbon\Carbon;

class ProximoAbonoService
{
    /**
     * Keys posibles de fecha en los abonos.
     */
    protected static array $dateKeys = [
        'fecha',
        'fecha_abono',
        'fecha_abono_compra',
        'fecha_abono_pedido',
    ];

    /**
     * Obtiene la última fecha válida de abono.
     */
    public static function obtenerUltimaFechaAbono(array $abonos): ?Carbon
    {
        $fechas = collect($abonos)
            ->map(function ($abono) {
                foreach (self::$dateKeys as $key) {
                    if (!empty($abono[$key])) {
                        try {
                            return Carbon::parse($abono[$key]);
                        } catch (\Throwable $e) {
                            return null;
                        }
                    }
                }
                return null;
            })
            ->filter()
            ->sort();

        return $fechas->last() ?: null;
    }

    /**
     * Calcula la próxima fecha de abono sumando plazo en días.
     */
    public static function obtenerProximaFechaAbono(array $abonos, int $plazoDias = 30): ?Carbon
    {
        $ultima = self::obtenerUltimaFechaAbono($abonos);
        if (!$ultima) {
            return null;
        }

        return $ultima->copy()->addDays($plazoDias);
    }

    /**
     * Días restantes hasta el próximo abono.
     * Retorna un entero con signo (+ / -).
     */
    public static function diasRestantes(array $abonos, int $plazoDias = 30): ?int
    {
        $proxima = self::obtenerProximaFechaAbono($abonos, $plazoDias);
        if (!$proxima) {
            return null;
        }

        return Carbon::today()->diffInDays($proxima, false);
    }

    /**
     * Determina el estado según los días restantes.
     */
    public static function estado(array $abonos, int $plazoDias = 30): ?string
    {
        $dias = self::diasRestantes($abonos, $plazoDias);
        if ($dias === null) return null;

        if ($dias < 0) return 'vencido';
        if ($dias === 0) return 'hoy';
        if ($dias <= 7) return 'proximo';
        return 'normal';
    }

    /**
     * Mensaje formateado igual a tu Resource original:
     * Ejemplos:
     * "Próximo abono: 12/03/2025 (en 7 días)"
     * "Próximo abono: 12/03/2025 (hoy)"
     * "Próximo abono: 12/03/2025 (vencido hace 3 días)"
     */
    public static function mensaje(array $abonos, int $plazoDias = 30): string
    {
        $proxima = self::obtenerProximaFechaAbono($abonos, $plazoDias);
        $dias = self::diasRestantes($abonos, $plazoDias);

        if (!$proxima || $dias === null) return '';

        $fecha = $proxima->format('d/m/Y');

        if ($dias > 0) {
            return "Próximo abono: {$fecha} (en {$dias} día" . ($dias === 1 ? '' : 's') . ")";
        }

        if ($dias === 0) {
            return "Próximo abono: {$fecha} (hoy)";
        }

        $venc = abs($dias);
        return "Próximo abono: {$fecha} (vencido hace {$venc} día" . ($venc === 1 ? '' : 's') . ")";
    }

    /**
     * Clases CSS según el estado (idéntico a tu lógica).
     */
    public static function estilo(array $abonos, int $plazoDias = 30): string
    {
        $estado = self::estado($abonos, $plazoDias);

        return match ($estado) {
            'vencido' => 'text-sm bg-red-600 text-red-50 mb-2 p-2 rounded',
            'hoy'     => 'text-sm bg-yellow-600 text-yellow-50 mb-2 p-2 rounded',
            'proximo' => 'text-sm bg-yellow-600 text-yellow-50 mb-2 p-2 rounded',
            'normal'  => 'text-sm bg-green-600 text-green-50 mb-2 p-2 rounded',
            default   => 'text-sm mb-2',
        };
    }
}
