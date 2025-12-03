<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use App\Models\Cliente;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\ClienteImporter;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\ClienteExporter;

class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(ClienteImporter::class)
                ->authorize(auth()->user()?->can('import', Cliente::class) ?? false),
            ExportAction::make()
                ->exporter(ClienteExporter::class)
                ->authorize(auth()->user()?->can('export', Cliente::class) ?? false),
        ];
    }
}
