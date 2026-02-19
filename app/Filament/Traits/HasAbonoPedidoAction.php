<?php

namespace App\Filament\Traits;

use App\Filament\Pages\AbonoPedido;
use Filament\Actions\Action;

trait HasAbonoPedidoAction
{
    protected function getAbonoPedidoAction(): Action
    {
        return Action::make('abonoPedido')
            ->label('Abono a Pedido')
            ->url(fn() => AbonoPedido::getUrl())
            ->icon('heroicon-o-banknotes')
            ->color('success');
    }
}
