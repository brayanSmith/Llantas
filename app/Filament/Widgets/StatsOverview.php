<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\IconPosition;
use App\Models\User;
use App\Models\Pedido;
use App\Services\ChartPedidoService;
use App\Filament\Traits\HasDateRangeFilter;
use App\Filament\Traits\HasCountTypeFilter;

class StatsOverview extends StatsOverviewWidget
{
    use HasDateRangeFilter, HasCountTypeFilter;
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected ?string $heading = 'Resumen de Pedidos';
    
    protected function getStats(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $query = Pedido::query();
        if ($startDate && $endDate) {
            $query->whereBetween('fecha', [$startDate, $endDate]);
        }

        // Determinar si mostrar cantidad o valor
        $isCantidad = $this->countType === 'cantidad';

        return [
            //
            Stat::make(
                $isCantidad ? 'Pedidos Facturados' : 'Total Facturado',
                $isCantidad 
                    ? (clone $query)->whereIn('estado', ['FACTURADO'])->count()
                    : '$' . number_format((clone $query)->whereIn('estado', ['FACTURADO'])->sum('total_a_pagar'), 0)
            )
            ->icon(Heroicon::OutlinedPresentationChartBar, IconPosition::Before)
            ->color('success')
            ->description($isCantidad ? 'Total de pedidos facturados' : 'Valor total facturado')
            ->chart(ChartPedidoService::obtenerVentasPorDia('FACTURADO')),
            
            Stat::make(
                $isCantidad ? 'Pedidos Pendientes' : 'Total Pendiente',
                $isCantidad 
                    ? (clone $query)->whereIn('estado', ['PENDIENTE'])->count()
                    : '$' . number_format((clone $query)->whereIn('estado', ['PENDIENTE'])->sum('total_a_pagar'), 0)
            )
            ->icon(Heroicon::OutlinedClock, IconPosition::Before)
            ->color('warning')
            ->description($isCantidad ? 'Total de pedidos pendientes' : 'Valor total pendiente')
            ->chart(ChartPedidoService::obtenerVentasPorDia('PENDIENTE')),

            Stat::make(
                $isCantidad ? 'Pedidos Entregados' : 'Total Entregado',
                $isCantidad 
                    ? (clone $query)->whereIn('estado', ['ENTREGADO'])->count()
                    : '$' . number_format((clone $query)->whereIn('estado', ['ENTREGADO'])->sum('total_a_pagar'), 0)
            )
            ->icon(Heroicon::OutlinedCheckCircle, IconPosition::Before)
            ->color('primary')
            ->description($isCantidad ? 'Total de pedidos entregados' : 'Valor total entregado')
            ->chart(ChartPedidoService::obtenerVentasPorDia('ENTREGADO')),

            Stat::make(
                $isCantidad ? 'Pedidos Anulados' : 'Total Anulado',
                $isCantidad 
                    ? (clone $query)->whereIn('estado', ['ANULADO'])->count()
                    : '$' . number_format((clone $query)->whereIn('estado', ['ANULADO'])->sum('total_a_pagar'), 0)
            )
            ->icon(Heroicon::OutlinedXCircle, IconPosition::Before)
            ->color('danger')
            ->description($isCantidad ? 'Total de pedidos anulados' : 'Valor total anulado')
            ->chart(ChartPedidoService::obtenerVentasPorDia('ANULADO')),
               
                
        ];
    }
}