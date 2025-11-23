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
            ImportColumn::make('categoria_producto')
                ->rules([
                    'nullable',
                    'in:MATERIA_PRIMA,PRODUCTO_TERMINADO,OTRO'
                ]),
            ImportColumn::make('codigo_producto')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('nombre_producto')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('descripcion_producto')
                ->rules(['max:255']),
            ImportColumn::make('costo_producto')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_detal_producto')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_mayorista_producto')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_ferretero_producto')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('imagen_producto')   
                ->rules(['max:255']),
            ImportColumn::make('medida_id')
                ->helperText('Seleccione el nombre de la medida existente')
                ->example('Kilogramo, Litro, Metro, Unidad')
                ->validationAttribute('Medida Invalida')
                //->numeric()
                //->relationship()
                //->multiple(',')
                //->relationship(resolveUsing: 'nombre_medida')
                /*->rules([
                    'nullable', 
                    'integer',
                    'exists:medidas,id'
                ])*/,
            ImportColumn::make('bodega_id') 
                ->numeric()
                ->rules([
                    'nullable', 
                    'integer',
                    'exists:bodegas,id'
                ]),
            ImportColumn::make('categoria_id')
                ->numeric()
                ->rules([
                    'nullable', 
                    'integer',
                    'exists:categorias,id'
                ]),
            ImportColumn::make('sub_categoria_id')
                ->numeric()
                ->rules([
                    'nullable', 
                    'integer',
                    'exists:sub_categorias,id'
                ]),
            ImportColumn::make('stock')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('entradas')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('salidas')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('activo')
                ->boolean()
                ->rules(['nullable', 'boolean']),
            ImportColumn::make('tipo_producto')
                ->rules(['max:255']),
            ImportColumn::make('peso_producto')
                ->numeric()
                ->rules([
                    'nullable',
                    'numeric',
                    'min:0',
                    'regex:/^\d{1,6}(\.\d{1,2})?$/',
                    function (string $attribute, mixed $value, \Closure $fail) {
                        if ($value !== null && !is_numeric($value)) {
                            $fail("El campo {$attribute} debe ser un número decimal válido (formato: 999999.99)");
                        }
                    }
                ]),
            ImportColumn::make('ubicacion_producto')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('alerta_producto')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('empaquetado_externo')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('empaquetado_interno')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('referencia_producto')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('codigo_cliente')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('volumen_producto')
                ->rules([
                    'nullable',
                    'in:EXTRA_GRANDE,GRANDE,MEDIANO,PEQUEÑO,EXTRA_PEQUEÑO'
                ]),
            ImportColumn::make('iva_producto')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('tipo_compra')
                ->rules([
                    'nullable',
                    'in:NACIONAL,IMPORTADO'
                ]),
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
