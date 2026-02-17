<?php

namespace App\Filament\Resources\AbonoCompras\Pages;

use App\Filament\Resources\AbonoCompras\AbonoCompraResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAbonoCompra extends ViewRecord
{
    protected static string $resource = AbonoCompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
