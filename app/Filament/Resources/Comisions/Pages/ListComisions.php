<?php

namespace App\Filament\Resources\Comisions\Pages;

use App\Filament\Resources\Comisions\ComisionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Tabs;

class ListComisions extends ListRecords
{
    protected static string $resource = ComisionResource::class;

    public function getTabs(): array 
    {
        return [
            //'Todos' => Tab::make(),
            'Pendientes' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('estado_comision', 'PENDIENTE')),
            'Pagadas' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('estado_comision', 'PAGADA')),
            'Rechazadas' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('estado_comision', 'RECHAZADA')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
