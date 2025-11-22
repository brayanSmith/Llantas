<?php

namespace App\Filament\Imports;

use App\Models\Producto;
use App\Models\Bodega;
use App\Models\Categoria;
use App\Models\SubCategoria;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProductoImporter extends Importer
{
    protected static ?string $model = Producto::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('categoria_producto')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('codigo_producto')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nombre_producto')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('descripcion_producto')
                ->rules(['max:255']),
            ImportColumn::make('costo_producto')
                ->requiredMapping()
                ->numeric()
                ->rules(['required']),
            ImportColumn::make('valor_detal_producto')
                ->requiredMapping()
                ->numeric()
                ->rules(['required']),
            ImportColumn::make('valor_mayorista_producto')
                ->requiredMapping()
                ->numeric()
                ->rules(['required']),
            ImportColumn::make('valor_ferretero_producto')
                ->requiredMapping()
                ->numeric()
                ->rules(['required']),
            ImportColumn::make('imagen_producto')   
                ->rules(['max:255']),
            ImportColumn::make('bodega_id')
                ->requiredMapping()
                ->numeric()
                ->rules([
                    'required', 
                    'integer',
                    function (string $attribute, mixed $value, \Closure $fail) {
                        if (!Bodega::where('id', $value)->exists()) {
                            $fail("No se encontró la bodega con el ID: {$value}");
                        }
                    }
                ]),
            ImportColumn::make('categoria_id')
                ->requiredMapping()
                ->numeric()
                ->rules([
                    'required', 
                    'integer',
                    function (string $attribute, mixed $value, \Closure $fail) {
                        if (!Categoria::where('id', $value)->exists()) {
                            $fail("No se encontró la categoría con el ID: {$value}");
                        }
                    }
                ]),
            ImportColumn::make('sub_categoria_id')
                ->requiredMapping()
                ->numeric()
                ->rules([
                    'required', 
                    'integer',
                    function (string $attribute, mixed $value, \Closure $fail) {
                        if (!SubCategoria::where('id', $value)->exists()) {
                            $fail("No se encontró la subcategoría con el ID: {$value}");
                        } else {
                            // Verificar que la subcategoría pertenezca a la categoría especificada
                            $subCategoria = SubCategoria::find($value);
                            $categoriaId = $this->data['categoria_id'] ?? null;
                            
                            if ($categoriaId && $subCategoria->categoria_id != $categoriaId) {
                                $fail("La subcategoría con ID: {$value} no pertenece a la categoría con ID: {$categoriaId}");
                            }
                        }
                    }
                ]),
            ImportColumn::make('stock')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('entradas')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('salidas')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('activo')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('tipo_producto')
                ->rules(['max:255']),
            ImportColumn::make('peso_producto')
                ->numeric(),
                //->rules(['double']),
            ImportColumn::make('ubicacion_producto')
                ->rules(['max:255']),
            ImportColumn::make('alerta_producto')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('empaquetado_externo')
                ->rules(['max:255']),
            ImportColumn::make('empaquetado_interno')
                ->rules(['max:255']),
            ImportColumn::make('referencia_producto')
                ->rules(['max:255']),
            ImportColumn::make('codigo_cliente')
                ->rules(['max:255']),
            ImportColumn::make('volumen_producto'),
            ImportColumn::make('iva_producto')
                ->requiredMapping()
                ->numeric()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): Producto
    {
        return Producto::firstOrNew([
            'codigo_producto' => $this->data['codigo_producto'],
        ]);
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
