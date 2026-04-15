<?php

namespace App\Filament\Widgets;

use App\Models\Producto;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Services\ChartPedidoService;

class ProductosChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected ?string $heading = 'Top 5 Productos Más Vendidos';

    protected function getData(): array
    {
        $bodegaId = $this->pageFilters['bodega_id'] ?? null;
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        $productosIds = $this->pageFilters['producto_id'] ?? null;

        return [
            'datasets' => [
                [
                    'label' => 'Total en Ventas ($)',
                    'data' => ChartPedidoService::getFiltroWidgets(
                        bodegaId: $bodegaId,
                        startDate: $startDate,
                        endDate: $endDate,
                        productoIds: $productosIds,
                        calculo: 'datos_ventas_producto'
                    ),
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
            'labels' => ChartPedidoService::getFiltroWidgets(
                        bodegaId: $bodegaId,
                        startDate: $startDate,
                        endDate: $endDate,
                        productoIds: $productosIds,
                        calculo: 'labels_ventas_producto'
                    ),
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
            'responsive' => true,
            'maintainAspectRatio' => false,
            'layout' => [
                'padding' => [
                    'left' => 10,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
                'y' => [
                    'ticks' => [
                        'autoSkip' => false,
                        'font' => [
                            'size' => 11,
                        ],
                        'callback' => null,
                    ],
                ],
            ],
        ];
    }
}
