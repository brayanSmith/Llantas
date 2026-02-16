<?php

namespace App\Filament\Resources\ComprasFacturadas\Pages;

use App\Filament\Resources\ComprasFacturadas\ComprasFacturadasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComprasFacturadas extends CreateRecord
{
    protected static string $resource = ComprasFacturadasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
