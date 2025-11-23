<?php

namespace App\Filament\Exports;

use App\Models\Producto;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ProductoExporter extends Exporter
{
    protected static ?string $model = Producto::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('categoria_producto'),
            ExportColumn::make('codigo_producto'),
            ExportColumn::make('nombre_producto'),
            ExportColumn::make('descripcion_producto'),
            ExportColumn::make('costo_producto'),
            ExportColumn::make('valor_detal_producto'),
            ExportColumn::make('valor_mayorista_producto'),
            ExportColumn::make('valor_ferretero_producto'),
            ExportColumn::make('iva_producto'),
            ExportColumn::make('imagen_producto'),
            ExportColumn::make('medida.nombre_medida'),
            ExportColumn::make('bodega.nombre_bodega'),
            ExportColumn::make('categoria.nombre_categoria'),
            ExportColumn::make('subCategoria.nombre_sub_categoria'),
            ExportColumn::make('stock'),
            ExportColumn::make('entradas'),
            ExportColumn::make('salidas'),
            ExportColumn::make('alerta_producto'),
            ExportColumn::make('activo'),
            ExportColumn::make('tipo_producto'),
            ExportColumn::make('tipo_compra'),
            ExportColumn::make('peso_producto'),
            ExportColumn::make('volumen_producto'),
            ExportColumn::make('ubicacion_producto'),
            ExportColumn::make('empaquetado_externo'),
            ExportColumn::make('empaquetado_interno'),
            ExportColumn::make('referencia_producto'),
            ExportColumn::make('codigo_cliente'),
            ExportColumn::make('concatenar_codigo_nombre'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
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
