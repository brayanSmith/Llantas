<?php

namespace App\Filament\Traits;

use App\Filament\Pages\AbonoCompra;
use Filament\Actions\Action;

trait HasAbonoAction
{
    protected function getAbonoAction(): Action
    {
        return Action::make('abonoCompra')
            ->label('Abono a Compra')
            ->url(fn() => AbonoCompra::getUrl())
            ->icon('heroicon-o-banknotes')
            ->color('success');
    }
}
