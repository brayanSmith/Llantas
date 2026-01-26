<?php

namespace App\Filament\Resources\PedidoCotizacions\Pages;

use App\Filament\Resources\PedidoCotizacions\PedidoCotizacionResource;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Livewire;
use App\Livewire\LivewireRepeaterPedido;

class CreatePedidoCotizacion extends CreateRecord
{
    protected static string $resource = PedidoCotizacionResource::class;

    public function getFormActions(): array
    {
        return [];
    }

}
