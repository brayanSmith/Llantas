<?php

namespace App\Filament\Resources\DetallePedidos\Pages;

use App\Filament\Resources\DetallePedidos\DetallePedidoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDetallePedidos extends ManageRecords
{
    protected static string $resource = DetallePedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
