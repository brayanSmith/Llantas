<?php

namespace App\Filament\Resources\PedidoEnRutas\Pages;

use App\Filament\Resources\PedidoEnRutas\PedidoEnRutaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPedidoEnRutas extends ListRecords
{
    protected static string $resource = PedidoEnRutaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }

}
