<?php

namespace App\Filament\Resources\Traslados\Pages;

use App\Filament\Resources\Traslados\TrasladoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTraslados extends ManageRecords
{
    protected static string $resource = TrasladoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
