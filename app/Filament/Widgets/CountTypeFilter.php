<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class CountTypeFilter extends Widget
{
    protected static ?int $sort = 0;
    
    protected int | string | array $columnSpan = 'full';

    public $countType = 'cantidad';

    public function updatedCountType($value)
    {
        // Emitir evento para que otros widgets se actualicen
        $this->dispatch('countTypeChanged', countType: $value);
    }

    public function getCountTypeOptions(): array
    {
        return [
            'cantidad' => 'Por Cantidad',
            'valor' => 'Por Valor Total',
        ];
    }

    public function render(): View
    {
        return view('filament.widgets.count-type-filter');
    }
}
