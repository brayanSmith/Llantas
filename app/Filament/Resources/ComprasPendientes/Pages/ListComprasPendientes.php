<?php

namespace App\Filament\Resources\ComprasPendientes\Pages;

use App\Filament\Resources\ComprasPendientes\ComprasPendientesResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;

class ListComprasPendientes extends ListRecords
{
    protected static string $resource = ComprasPendientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            /*Action::make('create')
                ->label('Crear Compra')
                ->icon('heroicon-o-plus')
                ->url(fn() => route('filament.admin.resources.compras-pendientes.create'))
                ->openUrlInNewTab(false)*/
        ];
    }
}
