<?php

namespace App\Filament\Resources\PedidoCotizacions\Schemas;

use App\Livewire\POS;
use App\Models\Pedido;
use Filament\Schemas\Schema;
use App\Livewire\PedidoFormLivewire;
use App\Livewire\LivewireRepeaterPedido;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Livewire;
use App\Livewire\Pedidos\LivewirePedidosForm;
use App\Filament\Forms\Components\InputReactive;
use App\Filament\Forms\Components\RepeaterPedido;
use App\Filament\Forms\Components\DropDownSearchable;
use App\Filament\Resources\PedidoCotizacions\Schemas\PedidoCotizacionDetail;
use App\Filament\Resources\PedidoCotizacions\Schemas\PedidoCotizacionDatosGenerales;

class PedidoCotizacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //...PedidoCotizacionDatosGenerales::sectionDatosGenerales(true),
                //...PedidoCotizacionDetail::sectionDetalles(),
                /*Livewire::make('repeater_pedido')
                ->component('livewire-repeater-pedido')
                ->relationship('detalles')
                ->key('mi-repeater-personalizado')
                ->columnSpanFull()*/
                //Livewire::make(PedidoFormLivewire::class)->columnSpanFull(),
                Livewire::make(POS::class)->columnSpanFull(),


            ]);
    }
}
