<?php

namespace App\Filament\Exports;

use App\Models\Producto;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class CatalogoExporter extends Exporter
{
    protected static ?string $model = Producto::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('stock_bodegas_sum_stock')
                ->label('Stock')
                ->sum('stockBodegas', 'stock'),
            ExportColumn::make('marca.marca')->label('Marca'),
            ExportColumn::make('descripcion_producto')->label('Descripción'),
            ExportColumn::make('referencia_producto')->label('Referencia'),
            ExportColumn::make('valor_mayorista')->label('Valor Mayorista'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your producto export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
