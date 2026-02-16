<?php

namespace App\Filament\Resources\PedidosPendientes\Pages;

use App\Filament\Resources\PedidosPendientes\PedidosPendientesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListPedidosPendientes extends ListRecords
{
    protected static string $resource = PedidosPendientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /*CreateAction::make(),*/

            /*Action::make('create')
                ->label('Crear')
                ->icon('heroicon-o-plus')
                ->url(fn() => route('filament.admin.resources.pedidos-pendientes.create'))
                ->openUrlInNewTab(false)*/
        ];
    }
}
