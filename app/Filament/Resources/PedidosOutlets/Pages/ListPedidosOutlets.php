<?php

namespace App\Filament\Resources\PedidosOutlets\Pages;

use App\Filament\Resources\PedidosOutlets\PedidosOutletResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPedidosOutlets extends ListRecords
{
    protected static string $resource = PedidosOutletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
