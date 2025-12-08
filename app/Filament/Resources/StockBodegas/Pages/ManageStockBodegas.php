<?php

namespace App\Filament\Resources\StockBodegas\Pages;

use App\Filament\Resources\StockBodegas\StockBodegaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStockBodegas extends ManageRecords
{
    protected static string $resource = StockBodegaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
