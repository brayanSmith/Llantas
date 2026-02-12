<?php

namespace App\Filament\Resources\PedidosPendientes\Schemas;

use Filament\Schemas\Schema;
use App\Livewire\PedidoFormLivewire;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Pedidos\Schemas\Concerns\HasPedidoSections;
//use App\Filament\Resources\Pedidos\Schemas\Concerns\HasPedidoSections;

class PedidosPendientesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(PedidoFormLivewire::class)->columnSpanFull(),

        ]);
    }
}
