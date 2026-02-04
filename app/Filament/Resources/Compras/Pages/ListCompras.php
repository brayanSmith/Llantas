<?php

namespace App\Filament\Resources\Compras\Pages;

use App\Filament\Resources\Compras\CompraResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Tabs;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ListCompras extends ListRecords
{
    protected static string $resource = CompraResource::class;

    public function getTabs(): array
    {
        return [
            'TODOS' => Tab::make(),
            'PRODUCTO' => Tab::make()
                ->modifyQueryUsing(fn(EloquentBuilder $query) => $query->where('item_compra', 'PRODUCTO')),
            'GASTO' => Tab::make()
                ->modifyQueryUsing(fn(EloquentBuilder $query) => $query->where('item_compra', 'GASTO')),
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
