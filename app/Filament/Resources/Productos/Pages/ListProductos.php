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
use App\Models\Categoria;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    public function getTabs(): array
    {
        $tabs = [
            'Todos' => Tab::make(),
            'Llantas' => Tab::make()->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria', 'LLANTA')),
            'Rines' => Tab::make()->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria', 'RIN')),
            'Otros' => Tab::make()->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('categoria', 'OTRO')),
        ];
        return $tabs;
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
