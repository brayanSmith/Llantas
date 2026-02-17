<?php

namespace App\Filament\Resources\AbonoCompras\Pages;

use App\Filament\Resources\AbonoCompras\AbonoCompraResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbonoCompra extends CreateRecord
{
    protected static string $resource = AbonoCompraResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getFormActions(): array
    {
        return [];
    }
}
