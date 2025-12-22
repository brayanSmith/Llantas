<?php

namespace App\Filament\Traits;

use Livewire\Attributes\On;

trait HasCountTypeFilter
{
    public $countType = 'cantidad';

    #[On('countTypeChanged')]
    public function updateCountType($countType)
    {
        $this->countType = $countType;
    }
}
