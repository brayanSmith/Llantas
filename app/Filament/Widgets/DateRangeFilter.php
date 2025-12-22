<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class DateRangeFilter extends Widget
{
    protected static ?int $sort = 0;
    
    protected int | string | array $columnSpan = 'full';

    public $dateRange = 'month';

    public function updatedDateRange($value)
    {
        // Emitir evento para que otros widgets se actualicen
        $this->dispatch('dateRangeChanged', dateRange: $value);
    }

    public function getDateRangeOptions(): array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Última Semana',
            'month' => 'Último Mes',
            'year' => 'Último Año',
            'all' => 'Todo el Tiempo',
        ];
    }

    public function render(): View
    {
        return view('filament.widgets.date-range-filter');
    }
}
