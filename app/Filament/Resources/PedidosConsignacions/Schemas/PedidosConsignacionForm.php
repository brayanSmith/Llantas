<?php

namespace App\Filament\Resources\PedidosConsignacions\Schemas;

use Filament\Schemas\Schema;
use App\Livewire\PedidoFormLivewire;
use Filament\Schemas\Components\Livewire;

class PedidosConsignacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Livewire::make(PedidoFormLivewire::class)->columnSpanFull(),
            ]);
    }
}
