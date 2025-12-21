<?php

namespace App\Filament\Resources\Productos\Pages;

use App\Filament\Resources\Productos\ProductoResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\SegmentadorProductosWidget;
use App\Filament\Widgets\ProductosActivosWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use App\Filament\Imports\ProductoImporter;
use Filament\Actions\ExportAction;
use App\Filament\Exports\ProductoExporter;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Tabs;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    public function getTabs(): array 
    {
        return [
            'Todos' => Tab::make(),
                //->badge(fn () => \App\Models\Pedido::count()),

            'Materia Prima' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria_producto', 'MATERIA_PRIMA')),
                //->badge(fn () => \App\Models\Pedido::where('estado', 'PENDIENTE')->count()),

            'Producto Terminado' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria_producto', 'PRODUCTO_TERMINADO')),
            // ->badge(fn () => \App\Models\Pedido::where('estado', 'FACTURADO')->count()),

            'Otro' => Tab::make()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria_producto', 'OTRO')),
            // ->badge(fn () => \App\Models\Pedido::where('estado', 'ANULADO')->count()),
        ];
    }

    protected function getTableActions(): array
    {
        return [


            Action::make('crearTraslado')
                ->label('Crear Traslado')
                ->color('success'),
        ];
    }

     protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(ProductoImporter::class),
            ExportAction::make()
                ->exporter(ProductoExporter::class),
        ];
    }
}
