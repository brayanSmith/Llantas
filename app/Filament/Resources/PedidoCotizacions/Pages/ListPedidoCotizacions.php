<?php

namespace App\Filament\Resources\PedidoCotizacions\Pages;

use App\Filament\Resources\PedidoCotizacions\PedidoCotizacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPedidoCotizacions extends ListRecords
{
    protected static string $resource = PedidoCotizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
