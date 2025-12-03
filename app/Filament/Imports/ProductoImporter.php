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
                ->helperText('Seleccione una de las opciones: MATERIA_PRIMA, PRODUCTO_TERMINADO, OTRO')
                ->example('PRODUCTO_TERMINADO')
                ->rules([
                    'nullable',
                    'in:MATERIA_PRIMA,PRODUCTO_TERMINADO,OTRO'
                ]),
            ImportColumn::make('codigo_producto')
                ->example('ABC123')
                ->helperText('Código único para cada producto')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('nombre_producto')
                ->helperText('Nombre del producto')
                ->example('Producto Ejemplo')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('descripcion_producto')
                ->helperText('Descripción del producto')
                ->example('Descripción Ejemplo')
                ->rules(['max:255']),
            ImportColumn::make('costo_producto')
                ->helperText('Costo del producto')
                ->numeric()
                ->example('100.50')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_detal_producto')
                ->helperText('Valor de venta al detalle del producto')
                ->numeric()
                ->example('150.75')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_mayorista_producto')
                ->numeric()
                ->helperText('Valor de venta al por mayor del producto')
                ->example('140.00')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('valor_ferretero_producto')
                ->numeric()
                ->helperText('Valor de venta al ferretero del producto')
                ->example('130.00')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('imagen_producto')   
                ->rules(['max:255']),
            ImportColumn::make('medida')
                ->helperText('Ingrese el nombre de la medida existente')
                ->validationAttribute('Medida inválida')
                ->relationship(resolveUsing: 'nombre_medida')
                ->example('Kilogramo')
                ->rules([
                    'nullable',
                    'max:255',
                ]),

            ImportColumn::make('bodega') 
                ->helperText('Ingrese el nombre de la bodega existente')
                ->validationAttribute('Bodega inválida')
                ->relationship(resolveUsing: 'nombre_bodega')
                ->example('Bodega Central')
                ->rules([
                    'nullable', 
                    'max:255', 
                ]),
            ImportColumn::make('categoria')
                ->helperText('Ingrese el nombre de la categoría existente')
                ->validationAttribute('Categoría inválida')
                ->relationship(resolveUsing: 'nombre_categoria')
                ->example('Categoría Ejemplo')
                ->rules([
                    'nullable', 
                    'max:255',
                ]),
            ImportColumn::make('subCategoria')
                ->helperText('Ingrese el nombre de la subcategoría existente')
                ->validationAttribute('Subcategoría inválida')
                ->relationship(resolveUsing: 'nombre_sub_categoria') 
                ->example('Subcategoría Ejemplo')
                ->rules([
                    'nullable', 
                    'max:255',
                ]),
            /*ImportColumn::make('stock')
                ->helperText('Cantidad disponible en stock')
                ->numeric()
                ->example('100')
                ->rules(['nullable', 'integer', 'min:0']), */          
            ImportColumn::make('activo')
                ->helperText('Ingrese 1 para activo o 0 para inactivo')
                ->boolean()
                ->example('1')
                ->rules(['nullable', 'boolean']),
            ImportColumn::make('tipo_producto')
                ->helperText('Seleccione una de las opciones: MATERIA_PRIMA, PRODUCTO_TERMINADO, OTRO')
                ->example('PRODUCTO_TERMINADO')
                ->rules(['max:255']),
            ImportColumn::make('peso_producto')
                ->helperText('Peso del producto en kilogramos')
                ->numeric()
                ->example('12.34')
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
                ->helperText('Ubicación del producto en el almacén')
                ->example('Estante A3')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('alerta_producto')
                ->helperText('Cantidad mínima en stock para generar una alerta')
                ->numeric()
                ->example('10')
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('empaquetado_externo')
                ->helperText('Tipo de empaquetado externo del producto')
                ->example('Caja Grande')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('empaquetado_interno')
                ->helperText('Tipo de empaquetado interno del producto')
                ->example('Caja')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('referencia_producto')
                ->helperText('Referencia del producto')
                ->example('REF12345')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('codigo_cliente')
                ->helperText('Código del cliente asociado al producto')
                ->example('CLI12345')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('volumen_producto')
                ->helperText('Volumen del producto')
                ->example('MEDIANO')
                ->rules([
                    'nullable',
                    'in:EXTRA_GRANDE,GRANDE,MEDIANO,PEQUEÑO,EXTRA_PEQUEÑO'
                ]),
            ImportColumn::make('iva_producto')
                ->helperText('IVA aplicado al producto')
                ->example('19')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('tipo_compra')
                ->helperText('Tipo de compra del producto')
                ->example('NACIONAL')
                ->rules([
                    'nullable',
                    'in:NACIONAL,IMPORTADO'
                ]),
        ];
    }

    public function beforeSave(): void
{
    $codigo = $this->record->codigo_producto ?? '';
    $nombre = $this->record->nombre_producto ?? '';

    $this->record->concatenar_codigo_nombre = "{$codigo} - {$nombre}";
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
