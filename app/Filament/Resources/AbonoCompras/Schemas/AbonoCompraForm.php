<?php

namespace App\Filament\Resources\AbonoCompras\Schemas;

use Filament\Schemas\Schema;
use App\Livewire\AbonoCompraFormLivewire;
use Filament\Schemas\Components\Livewire;

class AbonoCompraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Livewire::make(AbonoCompraFormLivewire::class)->columnSpanFull(),
            ]);
    }
}
