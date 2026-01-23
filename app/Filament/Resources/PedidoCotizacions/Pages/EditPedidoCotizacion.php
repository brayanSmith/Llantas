<?php

namespace App\Filament\Resources\PedidoCotizacions\Pages;

use App\Filament\Resources\PedidoCotizacions\PedidoCotizacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPedidoCotizacion extends EditRecord
{
    protected static string $resource = PedidoCotizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
