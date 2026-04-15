<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pedido;
use App\Filament\Traits\HasDateRangeFilter;
use App\Filament\Traits\HasCountTypeFilter;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Services\ChartPedidoService;

class PedidosAreaChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected ?string $heading = 'Tendencia de Pedidos';

    protected ?string $maxHeight = '300px';

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
                    //'data' => $datos->pluck('total')->toArray(),
                    'data' => ChartPedidoService::getFiltroWidgets(
                        bodegaId: $bodegaId,
                        startDate: $startDate,
                        endDate: $endDate,
                        productoIds: $productosIds,
                        calculo: 'datos_pedidos'
                    ),
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                    'tension' => 0,
                ],
                [
                    'label' => 'Cantidad de Pedidos',
                    //'data' => $datos->pluck('total')->toArray(),
                    'data' => ChartPedidoService::getFiltroWidgets(
                        bodegaId: $bodegaId,
                        startDate: $startDate,
                        endDate: $endDate,
                        productoIds: $productosIds,
                        calculo: 'datos_cantidad_pedidos'
                    ),
                    'fill' => true,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'borderWidth' => 2,
                    'tension' => 0,
                ],
            ],
            /*'labels' => $datos->map(function($item) {
                return Carbon::parse($item->fecha)->format('d/m');
            })->toArray(),*/
            'labels' => ChartPedidoService::getFiltroWidgets(
                bodegaId: $bodegaId,
                startDate: $startDate,
                endDate: $endDate,
                productoIds: $productosIds,
                calculo: 'labels_pedidos'
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'datalabels' => [
                    'display' => true,
                    'align' => 'top',
                    'color' => '#374151',
                    'font' => [
                        'weight' => 'bold',
                        'size' => 10,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
