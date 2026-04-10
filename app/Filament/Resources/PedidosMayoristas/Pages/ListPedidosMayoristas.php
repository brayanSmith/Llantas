<?php

namespace App\Filament\Resources\PedidosMayoristas\Pages;

use App\Filament\Resources\PedidosMayoristas\PedidosMayoristaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPedidosMayoristas extends ListRecords
{
    protected static string $resource = PedidosMayoristaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
