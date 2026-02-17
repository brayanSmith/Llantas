<?php

namespace App\Filament\Resources\AbonoCompras\Pages;

use App\Filament\Resources\AbonoCompras\AbonoCompraResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAbonoCompra extends EditRecord
{
    protected static string $resource = AbonoCompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
