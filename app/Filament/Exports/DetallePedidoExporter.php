<?php

namespace App\Filament\Exports;

use App\Models\DetallePedido;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class DetallePedidoExporter extends Exporter
{
    protected static ?string $model = DetallePedido::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            //Seccion de Pedido
            ExportColumn::make('pedido_id'),
            ExportColumn::make('pedido.codigo')
            ->label('Codigo Pedido'),
            ExportColumn::make('pedido.cliente.razon_social')
            ->label('Cliente'),
            ExportColumn::make('pedido.fecha')
            ->label('Fecha Pedido'),
            ExportColumn::make('pedido.estado')
            ->label('Estado Pedido'),
            ExportColumn::make('pedido.metodo_pago')
            ->label('Metodo Pago'),
            ExportColumn::make('pedido.tipo_precio')
            ->label('Tipo Precio'),
            ExportColumn::make('pedido.estado_pago')
            ->label('Estado Pago'),
            ExportColumn::make('pedido.bodega.nombre_bodega')
            ->label('Bodega'),
            //Seeccion de Producto
            ExportColumn::make('producto.codigo_producto')
            ->label('Codigo Producto'),
            ExportColumn::make('producto.nombre_producto')
            ->label('Nombre Producto'),
            ExportColumn::make('cantidad'),
            ExportColumn::make('precio_unitario'),
            ExportColumn::make('aplicar_iva'),
            ExportColumn::make('iva'),
            ExportColumn::make('precio_con_iva'),
            ExportColumn::make('subtotal'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your detalle pedido export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
