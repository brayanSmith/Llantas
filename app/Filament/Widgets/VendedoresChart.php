<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use App\Filament\Traits\HasDateRangeFilter;
use App\Filament\Traits\HasCountTypeFilter;

class VendedoresChart extends ChartWidget
{
    use HasDateRangeFilter, HasCountTypeFilter;
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;
    
    protected ?string $heading = 'Top 5 Mejores Vendedores';

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Obtener los 5 mejores vendedores con ambos datos
        $mejoresVendedores = User::query()
            ->role('COMERCIAL')
            ->withCount([
                'pedidos as cantidad' => function ($query) use ($startDate, $endDate) {
                    $query->whereIn('estado', ['FACTURADO', 'ENTREGADO']);
                    if ($startDate && $endDate) {
                        $query->whereBetween('fecha', [$startDate, $endDate]);
                    }
                }
            ])
            ->withSum([
                'pedidos as total_ventas' => function ($query) use ($startDate, $endDate) {
                    $query->whereIn('estado', ['FACTURADO', 'ENTREGADO']);
                    if ($startDate && $endDate) {
                        $query->whereBetween('fecha', [$startDate, $endDate]);
                    }
                }
            ], 'total_a_pagar')
            ->orderByDesc($this->countType === 'cantidad' ? 'cantidad' : 'total_ventas')
            ->limit(5)
            ->get();

        // Determinar qué datos mostrar según el filtro
        if ($this->countType === 'cantidad') {
            $data = $mejoresVendedores->pluck('cantidad')->toArray();
            $label = 'Cantidad de Pedidos';
        } else {
            $data = $mejoresVendedores->pluck('total_ventas')->toArray();
            $label = 'Total Ventas ($)';
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
            'labels' => $mejoresVendedores->pluck('name')->toArray(),
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
