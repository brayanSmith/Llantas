<?php

namespace App\Filament\Resources\PedidosEnCarteras\Schemas;

use Filament\Schemas\Schema;
use App\Livewire\PedidoFormLivewire;
use Filament\Schemas\Components\Livewire;

class PedidosEnCarteraForm
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
