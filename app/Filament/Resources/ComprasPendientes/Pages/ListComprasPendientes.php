<?php

namespace App\Filament\Resources\ComprasPendientes\Pages;

use App\Filament\Resources\ComprasPendientes\ComprasPendientesResource;
use App\Filament\Traits\HasAbonoAction;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;

class ListComprasPendientes extends ListRecords
{
    use HasAbonoAction;
    protected static string $resource = ComprasPendientesResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getAbonoAction(),
        ];
    }
}
