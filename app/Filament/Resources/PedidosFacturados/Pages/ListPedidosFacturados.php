<?php

namespace App\Filament\Resources\PedidosFacturados\Pages;

use App\Filament\Exports\DetallePedidoExporter;
use App\Filament\Exports\PedidoExporter;
use App\Filament\Resources\PedidosFacturados\PedidosFacturadosResource;
use App\Filament\Traits\HasAbonoPedidoAction;

use App\Models\DetallePedido;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPedidosFacturados extends ListRecords
{
    use HasAbonoPedidoAction;
    protected static string $resource = PedidosFacturadosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /*CreateAction::make()
                ->visible(fn() => static::getResource()::canCreate()),*/
            /*ExportAction::make()
                ->exporter(PedidoExporter::class),*/
            $this->getAbonoPedidoAction(),
            ExportAction::make('detalles')
                ->exporter(DetallePedidoExporter::class)
                ->modifyQueryUsing(fn(Builder $query) => DetallePedido::query()),
        ];
    }
}
