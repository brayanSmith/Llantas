<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use App\Filament\Resources\Clientes\Schemas\ClienteInfolist;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;

class ViewCliente extends ViewRecord
{
    protected static string $resource = ClienteResource::class;

    public function infolist(Schema $schema): Schema
    {
        return ClienteInfolist::configure($schema);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
