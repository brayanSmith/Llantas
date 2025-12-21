<?php

namespace App\Filament\Resources\Comisions\Pages;

use App\Filament\Resources\Comisions\ComisionResource;
use App\Filament\Resources\Comisions\Schemas\ComisionFormEstado;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewComision extends ViewRecord
{
    protected static string $resource = ComisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //EditAction::make(),
            ComisionFormEstado::getAction(),
        ];
    }
}
