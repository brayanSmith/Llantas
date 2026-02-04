<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\IconPosition;
use App\Services\ChartPedidoService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Resumen de Pedidos';

    protected function getStats(): array
    {

        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        $userIds = $this->pageFilters['user_id'] ?? null;
        $calculo = $this->pageFilters['calculo'] ?? 'valor';

        return [
            //

            Stat::make(
                label: 'Total pedidos facturados',
                value: ChartPedidoService::obtenerTotalPedidos(
                    estado: 'FACTURADO',
                    calculo: $calculo,
                    startDate: $startDate,
                    endDate: $endDate,
                    userIds: $userIds
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('success')
            ->description('Total de pedidos facturados')
            ->chart(ChartPedidoService::obtenerVentasPorDia('FACTURADO')),

            Stat::make(
                label: 'Total pedidos pendientes',
                value: ChartPedidoService::obtenerTotalPedidos(
                    estado: 'PENDIENTE',
                    calculo: $calculo,
                    startDate: $startDate,
                    endDate: $endDate,
                    userIds: $userIds
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('warning')
            ->description('Total de pedidos pendientes')
            ->chart(ChartPedidoService::obtenerVentasPorDia('PENDIENTE')),

            Stat::make(
                label: 'Total pedidos entregados',
                value: ChartPedidoService::obtenerTotalPedidos(
                    estado: 'ENTREGADO',
                    calculo: $calculo,
                    startDate: $startDate,
                    endDate: $endDate,
                    userIds: $userIds
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('primary')
            ->description('Total de pedidos entregados')
            ->chart(ChartPedidoService::obtenerVentasPorDia('ENTREGADO')),

            Stat::make(
                label: 'Total pedidos anulados',
                value: ChartPedidoService::obtenerTotalPedidos(
                    estado: 'ANULADO',
                    calculo: $calculo,
                    startDate: $startDate,
                    endDate: $endDate,
                    userIds: $userIds
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('danger')
            ->description('Total de pedidos anulados')
            ->chart(ChartPedidoService::obtenerVentasPorDia('ANULADO')),
        ];
    }
}
