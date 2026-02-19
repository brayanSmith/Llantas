<?php

namespace App\Filament\Resources\Pedidos\Schemas;

use Filament\Schemas\Schema;
use App\Filament\Resources\Pedidos\Schemas\Concerns\HasPedidoSections;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use App\Livewire\PedidoFormLivewire;
use Filament\Schemas\Components\Livewire;
class PedidoForm
{
    use HasPedidoSections;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(PedidoFormLivewire::class)->columnSpanFull(),

        ]);
    }
}


