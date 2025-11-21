<?php

namespace App\Filament\Resources\Gastos\Pages;

use App\Filament\Resources\Gastos\GastoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use App\Filament\Imports\GastoImporter;
use App\Filament\Exports\GastoExporter;

class ManageGastos extends ManageRecords
{
    protected static string $resource = GastoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
            ->importer(GastoImporter::class),
            ExportAction::make()
            ->exporter(GastoExporter::class),            
            CreateAction::make(),
        ];
    }
}
