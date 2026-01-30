<?php

namespace App\Filament\Resources\PedidoEnRutas\Pages;

use App\Filament\Resources\PedidoEnRutas\PedidoEnRutaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPedidoEnRuta extends EditRecord
{
    protected static string $resource = PedidoEnRutaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //DeleteAction::make(),
        ];
    }
}
