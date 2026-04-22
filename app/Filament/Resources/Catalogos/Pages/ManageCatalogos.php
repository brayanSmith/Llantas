<?php

namespace App\Filament\Resources\Catalogos\Pages;

use App\Filament\Exports\CatalogoExporter;
use App\Filament\Resources\Catalogos\CatalogoResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCatalogos extends ManageRecords
{
    protected static string $resource = CatalogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
            ExportAction::make()
                ->exporter(CatalogoExporter::class),
        ];
    }
}
