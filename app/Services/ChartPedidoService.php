<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class ChartPedidoService
{
    /**
     * Obtiene la cantidad de pedidos por día en un rango de días
     *
     * @param string $estado Estado del pedido (ej: 'FACTURADO', 'PENDIENTE')
     * @param int $dias Número de días hacia atrás (por defecto 7)
     * @return array Array con las cantidades por día
     */
    public static function obtenerVentasPorDia(string $estado, int $dias = 7): array
    {
        $ventasPorDia = Pedido::whereIn('estado', [$estado])
            ->whereBetween('fecha', [now()->subDays($dias - 1)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(fecha) as dia, COUNT(*) as cantidad')
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('cantidad')
            ->toArray();

        // Asegurar que siempre devolvemos un array con datos, incluso si están vacíos
        return array_pad($ventasPorDia, $dias, 0);
    }

    /**
     * Obtiene la cantidad de pedidos por múltiples estados
     *
     * @param array $estados Array de estados
     * @param int $dias Número de días hacia atrás
     * @return array
     */
    public static function obtenerVentasPorEstados(array $estados, int $dias = 7): array
    {
        $ventasPorDia = Pedido::whereIn('estado', $estados)
            ->whereBetween('fecha', [now()->subDays($dias - 1)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(fecha) as dia, COUNT(*) as cantidad')
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('cantidad')
            ->toArray();

        return array_pad($ventasPorDia, $dias, 0);
    }
}
