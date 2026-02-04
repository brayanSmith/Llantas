<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class VendedoresFilter extends Widget
{
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';

    public $selectedVendedores = [];
    public function updatedSelectedVendedores($value)
    {
        \Log::info('Vendedores seleccionados:', ['ids' => (array) $value]);
        // Emitir evento para que otros widgets se actualicen
        $this->dispatch('vendedoresChanged', vendedores: $value);
    }
    public function getUsersComerciales()
    {
        return User::role('Comercial')->get();
    }

    public function render(): View
    {
        return view('filament.widgets.vendedores-filter', [
            'users' => $this->getUsersComerciales(),
        ]);
    }
}
