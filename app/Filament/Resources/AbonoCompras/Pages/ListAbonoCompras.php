<?php

namespace App\Filament\Resources\AbonoCompras\Pages;

use App\Filament\Resources\AbonoCompras\AbonoCompraResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAbonoCompras extends ListRecords
{
    protected static string $resource = AbonoCompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
