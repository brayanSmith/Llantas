<?php

namespace App\Filament\Imports;

use App\Models\Producto;
use App\Models\Bodega;
use App\Models\Categoria;
use App\Models\SubCategoria;
use App\Models\Medida;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProductoImporter extends Importer
{
    protected static ?string $model = Producto::class;

    protected static ?int $chunkSize = 100;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('categoria')
                ->helperText('Ingrese el nombre de la categoría (LLANTA, RIN, SERVICIO, OTRO)')
                ->validationAttribute('Categoría inválida')
                ->rules([
                    'required',
                    'max:255',
                ]),
            ImportColumn::make('tipo')
                ->helperText('Ingrese el tipo del producto (NUEVO, USADO)')
                ->validationAttribute('Tipo de producto inválido')
                ->rules([
                    'required',
                    'max:255',
                ]),
            ImportColumn::make('inventariable')
                ->helperText('Indique si el producto es inventariable (true/false)')
                ->validationAttribute('Valor de inventariable inválido')
                ->boolean()
                ->rules(['required', 'boolean']),

            ImportColumn::make('marca')
                ->helperText('Ingrese el nombre de la marca existente')
                ->validationAttribute('Marca inválida')
                ->relationship(resolveUsing: 'marca')
                ->example('Marca Ejemplo')
                ->rules([
                    'nullable',
                    'max:255',
                    'exists:marcas,marca',
                ]),
            /*ImportColumn::make('referencia_producto')
                ->helperText('Referencia del producto')
                ->validationAttribute('Referencia del producto inválida')
                ->example('REF12345')
                ->rules(['nullable', 'max:255']),*/
            ImportColumn::make('descripcion_producto')
                ->helperText('Descripción del producto')
                ->validationAttribute('Descripción del producto inválida')
                ->example('Descripción Ejemplo')
                ->rules(['max:255']),
            ImportColumn::make('costo_producto')
                ->helperText('Costo del producto')
                ->validationAttribute('Costo del producto inválido')
                ->numeric()
                ->example('100.50')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_detal')
                ->helperText('Valor de venta al detalle del producto')
                ->validationAttribute('Valor al detalle del producto inválido')
                ->numeric()
                ->example('150.75')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_mayorista')
                ->numeric()
                ->validationAttribute('Valor al por mayor del producto inválido')
                ->helperText('Valor de venta al por mayor del producto')
                ->example('140.00')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_sin_instalacion')
                ->numeric()
                ->validationAttribute('Valor sin instalación del producto inválido')
                ->helperText('Valor de venta sin instalación del producto')
                ->example('130.00')
                ->rules(['nullable', 'numeric', 'min:0']),

        ];
    }


    public function resolveRecord(): Producto
    {
        // Usar firstOrNew solo si se pretende actualizar registros existentes
        // Si solo se quieren crear nuevos, usar new Producto()
        return new Producto();

        // Descomenta la línea siguiente si quieres permitir actualizaciones de productos existentes:
        // return Producto::firstOrNew(['codigo_producto' => $this->data['codigo_producto']]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your producto import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
