<?php

namespace App\Filament\Resources\PedidoDomiciliarios\Pages;

use App\Filament\Resources\PedidoDomiciliarios\PedidoDomiciliarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePedidoDomiciliarios extends ManageRecords
{
    protected static string $resource = PedidoDomiciliarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn() => static::getResource()::canCreate()),
        ];
    }
}
