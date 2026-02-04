<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class ChartPedidoService
{
    public static function obtenerTotalPedidos(string $estado, string $calculo, ?string $startDate = null, ?string $endDate = null, ?array $userIds = null): float|int|string
    {
        $query = Pedido::whereIn('estado', [$estado]);

        if ($startDate && $endDate) {
            $query->whereBetween('fecha', [$startDate, $endDate]);
        }

        if ($userIds && count($userIds) > 0) {
            $query->whereIn('user_id', $userIds);
        }

        if ($calculo === 'valor') {
            $valor = $query->sum('total_a_pagar');
            // Retornar string con formato decimal y separador de miles
            return number_format($valor, 2, ',', '.');
        }
        // Retornar entero para cantidad
        return $query->count();
    }

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

    /**
     * Retorna un array con el total a pagar o la cantidad de pedidos agrupados por fecha
     *
     * @param string $estado
     * @param string $calculo ('valor' para total, 'cantidad' para cantidad)
     * @param string|null $startDate
     * @param string|null $endDate
     * @param array|null $userIds
     * @return array
     */
    public static function obtenerTotalesPorFecha(string $estado, string $calculo, ?string $startDate = null, ?string $endDate = null, ?array $userIds = null): array
    {
        $query = Pedido::whereIn('estado', [$estado]);

        if ($startDate && $endDate) {
            $query->whereBetween('fecha', [$startDate, $endDate]);
        }

        if ($userIds && count($userIds) > 0) {
            $query->whereIn('user_id', $userIds);
        }

        if ($calculo === 'valor') {
            // Agrupar y sumar total_a_pagar por fecha
            return $query->selectRaw('DATE(fecha) as dia, SUM(total_a_pagar) as total')
                ->groupBy('dia')
                ->orderBy('dia')
                ->pluck('total', 'dia')
                ->toArray();
        }
        // Agrupar y contar cantidad por fecha
        return $query->selectRaw('DATE(fecha) as dia, COUNT(*) as cantidad')
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('cantidad', 'dia')
            ->toArray();
    }
}
