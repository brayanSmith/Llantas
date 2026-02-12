<?php

namespace App\Filament\Resources\PedidosFacturados\Schemas;
use App\Filament\Resources\Pedidos\Schemas\Concerns\HasPedidoSections;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use App\Livewire\PedidoFormLivewire;
use Filament\Schemas\Components\Livewire;

use Filament\Schemas\Schema;

class PedidosFacturadosForm
{
    use HasPedidoSections;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(PedidoFormLivewire::class)->columnSpanFull(),

        ]);
    }
}
