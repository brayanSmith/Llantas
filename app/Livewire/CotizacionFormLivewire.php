<?php

namespace App\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\Producto;
use App\Models\Bodega;

class CotizacionFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $productos = [];
    public ?array $bodegas = [];

    public function mount(): void
    {
        $this->productos = Producto::with('stockBodegas')->get()->toArray();
        $this->bodegas = Bodega::all()->toArray();
    }



    public function render(): View
    {
        return view('livewire.cotizacion-form-livewire', [
            'productos' => $this->productos,
            'bodegas' => $this->bodegas,
        ]);
    }
}
