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
        $bodegaId = $this->pageFilters['bodega_id'] ?? null;
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        $productosIds = $this->pageFilters['producto_id'] ?? null;
        //$userIds = $this->pageFilters['user_id'] ?? null;
        //$calculo = $this->pageFilters['calculo'] ?? 'valor';

        return [
            //

            Stat::make(
                label: 'Total Pedidos',
                value: ChartPedidoService::getFiltroWidgets(
                    bodegaId: $bodegaId,
                    startDate: $startDate,
                    endDate: $endDate,
                    productoIds: $productosIds,
                    calculo: 'cantidad'
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('success')
            ->description('Total de pedidos')
            ->chart(ChartPedidoService::obtenerVentasPorDia('COMPLETADO')),

            Stat::make(
                label: 'Valor Pedidos',
                value: ChartPedidoService::getFiltroWidgets(
                    bodegaId: $bodegaId,
                    startDate: $startDate,
                    endDate: $endDate,
                    productoIds: $productosIds,
                    calculo: 'valor_venta'
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('warning')
            ->description('Valor total de pedidos')
            ->chart(ChartPedidoService::obtenerVentasPorDia('PENDIENTE')),

            Stat::make(
                label: 'Valor Inversion',
                value: ChartPedidoService::getFiltroWidgets(
                    bodegaId: $bodegaId,
                    startDate: $startDate,
                    endDate: $endDate,
                    productoIds: $productosIds,
                    calculo: 'inversion'
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('warning')
            ->description('Valor total de inversión')
            ->chart(ChartPedidoService::obtenerVentasPorDia('PENDIENTE')),

            /*Stat::make(
                label: 'Valor Gasto',
                value: ChartPedidoService::getFiltroWidgets(
                    bodegaId: $bodegaId,
                    startDate: $startDate,
                    endDate: $endDate,
                    productoIds: $productosIds,
                    calculo: 'gasto'
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('danger')
            ->description('Valor total de gasto')
            ->chart(ChartPedidoService::obtenerVentasPorDia('PENDIENTE')),*/

            Stat::make(
                label: 'Valor Ganancia',
                value: ChartPedidoService::getFiltroWidgets(
                    bodegaId: $bodegaId,
                    startDate: $startDate,
                    endDate: $endDate,
                    productoIds: $productosIds,
                    calculo: 'ganancia'
                ),
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('warning')
            ->description('Valor total de ganancia')
            ->chart(ChartPedidoService::obtenerVentasPorDia('PENDIENTE')),

            /*Stat::make(
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
            ->chart(ChartPedidoService::obtenerVentasPorDia('ANULADO')),*/
        ];
    }
}
