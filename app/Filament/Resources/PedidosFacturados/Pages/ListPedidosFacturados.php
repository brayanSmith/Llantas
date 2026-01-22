<?php

namespace App\Filament\Resources\PedidosFacturados\Pages;

use App\Filament\Exports\DetallePedidoExporter;
use App\Filament\Resources\PedidosFacturados\PedidosFacturadosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use App\Filament\Exports\PedidoExporter;
use Filament\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DetallePedido;

class ListPedidosFacturados extends ListRecords
{
    protected static string $resource = PedidosFacturadosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /*CreateAction::make()
                ->visible(fn() => static::getResource()::canCreate()),*/
            /*ExportAction::make()
                ->exporter(PedidoExporter::class),*/
            ExportAction::make('detalles')
                ->exporter(DetallePedidoExporter::class)
                ->modifyQueryUsing(fn(Builder $query) => DetallePedido::query()),
        ];
    }
}
