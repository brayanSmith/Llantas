<?php

namespace App\Filament\Resources\PedidosConsignacions\Pages;

use App\Filament\Resources\PedidosConsignacions\PedidosConsignacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPedidosConsignacions extends ListRecords
{
    protected static string $resource = PedidosConsignacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
