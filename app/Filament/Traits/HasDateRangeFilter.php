<?php

namespace App\Filament\Traits;

use Carbon\Carbon;
use Livewire\Attributes\On;

trait HasDateRangeFilter
{
    public $dateRange = 'month';

    #[On('dateRangeChanged')]
    public function updateDateRange($dateRange)
    {
        $this->dateRange = $dateRange;
    }

    protected function getDateRange(): array
    {
        return match($this->dateRange) {
            'today' => [Carbon::today(), Carbon::now()],
            'week' => [Carbon::now()->subWeek(), Carbon::now()],
            'month' => [Carbon::now()->subMonth(), Carbon::now()],
            'year' => [Carbon::now()->subYear(), Carbon::now()],
            'all' => [null, null],
            default => [Carbon::now()->subMonth(), Carbon::now()],
        };
    }
}
