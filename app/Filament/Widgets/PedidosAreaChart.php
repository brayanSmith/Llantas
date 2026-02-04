<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pedido;
use App\Filament\Traits\HasDateRangeFilter;
use App\Filament\Traits\HasCountTypeFilter;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

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
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        $userIds = $this->pageFilters['user_id'] ?? null;
        $calculo = $this->pageFilters['calculo'] ?? 'valor';

        $query = Pedido::query()
            ->whereIn('estado', ['FACTURADO', 'ENTREGADO'])
            ->whereIn('user_id', $userIds ?? Pedido::pluck('user_id')->toArray());

        if ($startDate && $endDate) {
            $query->whereBetween('fecha', [$startDate, $endDate]);
        }

        // Agrupar por fecha
        if ($calculo === 'cantidad') {
            $datos = $query
                ->selectRaw('DATE(fecha) as fecha, COUNT(*) as total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            $label = 'Cantidad de Pedidos';
        } else {
            $datos = $query
                ->selectRaw('DATE(fecha) as fecha, SUM(total_a_pagar) as total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            $label = 'Total en Ventas ($)';
        }

        return [
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $datos->pluck('total')->toArray(),
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                    'tension' => 0,
                ],
            ],
            'labels' => $datos->map(function($item) {
                return Carbon::parse($item->fecha)->format('d/m');
            })->toArray(),
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
