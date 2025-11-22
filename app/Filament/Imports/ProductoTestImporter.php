<?php

namespace App\Filament\Imports;

use App\Models\Producto;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProductoTestImporter extends Importer
{
    protected static ?string $model = Producto::class;

    public static function getColumns(): array
    {
        return [
            // Solo campos básicos para probar
            ImportColumn::make('categoria_producto')
                ->rules(['nullable', 'in:MATERIA_PRIMA,PRODUCTO_TERMINADO,OTRO']),
                
            ImportColumn::make('codigo_producto')
                ->rules(['nullable', 'max:255']),
                
            ImportColumn::make('nombre_producto')
                ->rules(['nullable', 'max:255']),
                
            ImportColumn::make('iva_producto')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:1']),
                
            // Test con un campo que seguramente falle
            ImportColumn::make('test_field')
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): Producto
    {
        return new Producto();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Test import completed: ' . Number::format($import->successful_rows) . ' rows imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' rows failed.';
        }

        return $body;
    }
}