<?php

namespace App\Filament\Resources\ComprasFacturadas\Schemas;

use App\Filament\Resources\Compras\Schemas\Concerns\HasCompraSections;
use App\Livewire\CompraFormLivewire;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;


class ComprasFacturadasForm
{
    use HasCompraSections;
    public static function configure(Schema $schema): Schema
    {

            return $schema->components([
            Livewire::make(CompraFormLivewire::class)->columnSpanFull(),
        ]);
    }
}
