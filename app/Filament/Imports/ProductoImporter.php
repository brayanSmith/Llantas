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
                ->helperText('Ingrese el nombre de la categoría existente')
                ->validationAttribute('Categoría inválida')
                ->rules([
                    'required',
                    'max:255',
                ]),
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
            /*ImportColumn::make('porcentaje_valor_detal')
                ->helperText('Porcentaje de ganancia para valor al detalle')
                ->validationAttribute('Porcentaje de ganancia para valor al detalle inválido')
                ->numeric()
                ->example('20.00')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('porcentaje_valor_mayorista')
                ->helperText('Porcentaje de ganancia para valor al por mayor')
                ->validationAttribute('Porcentaje de ganancia para valor al por mayor inválido')
                ->numeric()
                ->example('15.00')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('porcentaje_valor_sin_instalacion')
                ->helperText('Porcentaje de ganancia para valor sin instalación')
                ->validationAttribute('Porcentaje de ganancia para valor sin instalación inválido')
                ->numeric()
                ->example('10.00')
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('porcentaje_dinamico')
                ->helperText('Indica si el porcentaje de ganancia es dinámico (1 para sí, 0 para no)')
                ->validationAttribute('Porcentaje de ganancia dinámico inválido')
                ->boolean()
                ->example('1')
                ->rules(['nullable', 'boolean']),*/
            ImportColumn::make('atributos_adicionales')
                ->helperText('No mapees esta columna, se procesará automáticamente desde las columnas con prefijo attr_'),
            //->ignore(true), // Ignoramos porque no existe en la tabla productos
        ];
    }
    public function beforeSave(): void
    {
        // Log para depuración de la categoría recibida
        \Log::info('Importando producto - valor recibido de categoria', [
            'categoria_en_data' => $this->data['categoria'] ?? null,
            'data_completa' => $this->data
        ]);

        // Mapear el nombre de la categoría al categoria_id
        if (!empty($this->data['categoria'])) {
            $categoria = Categoria::where('nombre_categoria', $this->data['categoria'])
                ->orWhere('id', $this->data['categoria'])
                ->first();
            \Log::info('Resultado búsqueda de categoría', [
                'busqueda_por' => $this->data['categoria'],
                'categoria_encontrada' => $categoria ? $categoria->toArray() : null
            ]);
            if ($categoria) {
                $this->record->categoria_id = $categoria->id;
            } else {
                // Lanzar excepción si la categoría no existe
                throw new \Exception("La categoría '{$this->data['categoria']}' no existe en el sistema.");
            }
        }

        // Construir referencia_producto dinámicamente según los atributos de la categoría y su orden
        $referencia = '';
        if (!empty($this->record->categoria_id)) {
            $atributosCategoria = \App\Models\Atributo::where('categoria_id', $this->record->categoria_id)
                ->orderBy('orden')
                ->get();
            foreach ($atributosCategoria as $atributo) {
                $key = 'attr_' . $atributo->nombre;
                if (!empty($this->data[$key])) {
                    $referencia .= $this->data[$key];
                }
            }
        }
        if (!empty($referencia)) {
            $this->record->referencia_producto = $referencia;
        }

        // Construir concatenar_codigo_nombre a partir de referencia_producto + marca + descripcion_producto
        $referencia = $this->record->referencia_producto ?? '';
        $marca = $this->data['marca'] ?? '';
        $descripcion = $this->data['descripcion_producto'] ?? '';

        $partes = array_filter([$referencia, $marca, $descripcion]);
        $this->record->concatenar_codigo_nombre = implode(' - ', $partes);

        // Remover el atributo categoria para evitar que se guarde en la BD
        unset($this->record->categoria);
    }

    public function afterSave(): void
    {
        $producto = $this->record;
        $rawData = $this->data; // Datos crudos de la fila del Excel

        // Debug: Ver qué datos tenemos
        \Log::info('ProductoImporter afterSave', [
            'producto_id' => $producto->id,
            'categoria_id' => $producto->categoria_id,
            'rawData_keys' => array_keys($rawData),
            'rawData' => $rawData
        ]);

        foreach ($rawData as $key => $value) {
            // Buscamos columnas que empiecen con attr_
            if (str_starts_with($key, 'attr_') && !empty($value)) {
                $nombreAtributo = str_replace('attr_', '', $key);

                \Log::info('Procesando atributo', [
                    'key' => $key,
                    'nombreAtributo' => $nombreAtributo,
                    'value' => $value,
                    'categoria_id' => $producto->categoria_id
                ]);

                // Buscamos el atributo que pertenezca a la categoría del producto importado
                $atributo = \App\Models\Atributo::where('nombre', $nombreAtributo)
                    ->where('categoria_id', $producto->categoria_id)
                    ->first();

                if ($atributo) {
                    \Log::info('Atributo encontrado, guardando', [
                        'atributo_id' => $atributo->id,
                        'nombre' => $atributo->nombre
                    ]);

                    // Guardamos en la tabla pivote que definimos antes
                    // Usamos updateOrCreate por si acaso se re-importa
                    \App\Models\AtributoProducto::updateOrCreate(
                        [
                            'producto_id' => $producto->id,
                            'atributo_id' => $atributo->id,
                        ],
                        ['valor' => $value]
                    );
                } else {
                    \Log::warning('Atributo NO encontrado', [
                        'nombreAtributo' => $nombreAtributo,
                        'categoria_id' => $producto->categoria_id
                    ]);
                }
            }
        }
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
