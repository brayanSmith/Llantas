<?php

namespace App\Filament\Resources\ComprasFacturadas\Pages;

use App\Filament\Exports\CompraExporter;
use App\Filament\Resources\ComprasFacturadas\ComprasFacturadasResource;
use App\Filament\Traits\HasAbonoAction;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListComprasFacturadas extends ListRecords
{
    use HasAbonoAction;
    protected static string $resource = ComprasFacturadasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
            $this->getAbonoAction(),
            ExportAction::make()
            ->exporter(CompraExporter::class),


        ];
    }
}
