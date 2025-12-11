<?php

namespace App\Filament\Resources\PedidosFacturados\Pages;

use App\Filament\Resources\PedidosFacturados\PedidosFacturadosResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePedidosFacturados extends CreateRecord
{
    protected static string $resource = PedidosFacturadosResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
