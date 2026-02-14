<?php

namespace App\Filament\Resources\ComprasPendientes\Schemas;

use App\Filament\Resources\Compras\Schemas\Concerns\HasCompraSections;
use Filament\Schemas\Schema;
use App\Livewire\CompraFormLivewire;
use Filament\Schemas\Components\Livewire;

class ComprasPendientesForm
{
    use HasCompraSections;
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(CompraFormLivewire::class)->columnSpanFull(),
        ]);
    }
}
