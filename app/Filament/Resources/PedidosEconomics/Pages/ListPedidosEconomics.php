<?php

namespace App\Filament\Resources\PedidosEconomics\Pages;

use App\Filament\Resources\PedidosEconomics\PedidosEconomicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPedidosEconomics extends ListRecords
{
    protected static string $resource = PedidosEconomicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
