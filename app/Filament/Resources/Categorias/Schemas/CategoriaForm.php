<?php

namespace App\Filament\Resources\Categorias\Schemas;

use App\Livewire\CategoriaFormLivewire;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Livewire;

class CategoriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Livewire::make(CategoriaFormLivewire::class)->columnSpanFull(),
            ]);
    }
}
