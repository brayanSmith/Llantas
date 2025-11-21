<?php

namespace App\Filament\Resources\Pucs\Pages;

use App\Filament\Resources\Pucs\PucResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use App\Filament\Imports\PucImporter;
use App\Filament\Exports\PucExporter;

class ManagePucs extends ManageRecords
{
    protected static string $resource = PucResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
            ->importer(PucImporter::class),
            ExportAction::make()
            ->exporter(PucExporter::class), 
            CreateAction::make(),

        ];
    }
}
