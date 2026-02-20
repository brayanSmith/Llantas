<?php

namespace App\Filament\Traits;

use Filament\Actions\Action;

trait HasEditarAction
{
    protected function getEditarAction($route): Action
    {
        return Action::make('edit')
            ->label('Editar')
            ->icon('heroicon-o-pencil')
            ->url(fn($record) => route($route, ['record' => $record->getKey(), 'pedido_id' => $record->getKey()]))
            ->openUrlInNewTab(false);
    }
}
