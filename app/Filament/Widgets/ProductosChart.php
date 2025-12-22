<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Producto;
use App\Filament\Traits\HasDateRangeFilter;
use App\Filament\Traits\HasCountTypeFilter;

class ProductosChart extends ChartWidget
{
    use HasDateRangeFilter, HasCountTypeFilter;
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    
    protected ?string $heading = 'Top 5 Productos Más Vendidos';

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Obtener los 5 productos más vendidos con ambos datos
        $productosVendidos = Producto::query()
            ->where('categoria_producto', 'PRODUCTO_TERMINADO')
            ->withSum([
                'detallePedidos as cantidad_vendida' => function ($query) use ($startDate, $endDate) {
                    $query->whereHas('pedido', function($q) use ($startDate, $endDate) {
                        $q->whereIn('estado', ['FACTURADO', 'ENTREGADO']);
                        if ($startDate && $endDate) {
                            $q->whereBetween('fecha', [$startDate, $endDate]);
                        }
                    });
                }
            ], 'cantidad')
            ->withSum([
                'detallePedidos as total_ventas' => function ($query) use ($startDate, $endDate) {
                    $query->whereHas('pedido', function($q) use ($startDate, $endDate) {
                        $q->whereIn('estado', ['FACTURADO', 'ENTREGADO']);
                        if ($startDate && $endDate) {
                            $q->whereBetween('fecha', [$startDate, $endDate]);
                        }
                    });
                }
            ], 'subtotal')
            ->orderByDesc($this->countType === 'cantidad' ? 'cantidad_vendida' : 'total_ventas')
            ->limit(5)
            ->get();

        // Determinar qué datos mostrar según el filtro
        if ($this->countType === 'cantidad') {
            $data = $productosVendidos->pluck('cantidad_vendida')->toArray();
            $label = 'Cantidad Vendida (Unidades)';
        } else {
            $data = $productosVendidos->pluck('total_ventas')->toArray();
            $label = 'Total en Ventas ($)';
        }

        return [
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // Verde
                        'rgba(59, 130, 246, 0.8)',  // Azul
                        'rgba(251, 146, 60, 0.8)',  // Naranja
                        'rgba(168, 85, 247, 0.8)',  // Morado
                        'rgba(236, 72, 153, 0.8)',  // Rosa
                    ],
                    'borderColor' => [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(251, 146, 60, 1)',
                        'rgba(168, 85, 247, 1)',
                        'rgba(236, 72, 153, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $productosVendidos->pluck('nombre_producto')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
