<?php

namespace App\Livewire;

use App\Models\AtributoProducto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ProductoFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithFileUploads;

    public ?Producto $producto = null;
    public ?array $categoria = [];
    public ?array $marca = [];
    public ?array $categorias = [];
    public ?array $marcas = [];
    public bool $esEdicion = false;

        public $imagen_producto = null;
    public ?string $imagenProductoPath = null;

    protected function getProductoDefaults(): array
    {
        return [
            'referencia_producto' => '',
            'descripcion_producto' => '',
            'costo_producto' => 0,
            'valor_detal' => 0,
            'valor_mayorista' => 0,
            'valor_sin_instalacion' => 0,
            'porcentaje_valor_detal' => 0,
            'porcentaje_valor_mayorista' => 0,
            'porcentaje_valor_sin_instalacion' => 0,
            'porcentaje_dinamico' => false,
            'categoria_id' => null,
            'marca_id' => null,
        ];
    }

    public function mount(): void
    {
        // Intenta obtener el ID desde la ruta primero (para Filament)
        $productoId = request()->route('record');

        // Si no está en la ruta, intenta desde query string
        if (!$productoId) {
            $productoId = request()->get('producto_id');
        }
        // Convierte a ID si es un modelo
        if ($productoId && is_object($productoId)) {
            $productoId = $productoId->id;
        }
        $this->producto = null;
        if ($productoId) {
            $this->esEdicion = true;
            $productoEncontrado = Producto::with(['categoria', 'marca', 'atributoProductos'])->find($productoId);
            if ($productoEncontrado) {
                $this->producto = $productoEncontrado;
                $this->categoria = $productoEncontrado->categoria ? $productoEncontrado->categoria->toArray() : [];
                $this->marca = $productoEncontrado->marca ? $productoEncontrado->marca->toArray() : [];

                // Cargar path de imagen si existe
                if ($productoEncontrado->imagen_producto) {
                    $this->imagenProductoPath = Storage::url('productos/' . $productoEncontrado->imagen_producto);
                }
            }
        }
        // Cargar todas las categorías y marcas disponibles
        $this->categorias = Categoria::with('atributos')->get()->toArray();
        $this->marcas = Marca::get()->toArray();
    }

    public function guardarProducto($datos): void
    {
        try {
            $productoData = $datos;
            $atributosDetalle = $productoData['atributo_productos'] ?? [];
            unset($productoData['atributo_productos']);

            // Guardar la imagen si existe
            $rutaImagen = null;
            if ($this->imagen_producto) {
                $rutaImagen = $this->imagen_producto->store('productos', 'public');
                $productoData['imagen_producto'] = basename($rutaImagen);
            }

            // Guardar o actualizar producto
            if(isset($productoData['id']) && $this->esEdicion){
                $productoModel = Producto::find($productoData['id']);
                if ($productoModel) {
                    $productoModel->update($productoData);
                }
            } else {
                $productoModel = Producto::create($productoData);
            }

            if (isset($productoModel)) {
                AtributoProducto::where('producto_id', $productoModel->id)->delete();
                foreach ($atributosDetalle as $atributo) {
                    if (!isset($atributo['atributo_id'])) {
                        continue;
                    }
                    $valor = $atributo['valor'] ?? null;
                    if ($valor === null || trim((string) $valor) === '') {
                        continue;
                    }
                    AtributoProducto::create([
                        'producto_id' => $productoModel->id,
                        'atributo_id' => $atributo['atributo_id'],
                        'valor' => $valor,
                    ]);
                }
            }

            $this->imagen_producto = null;
            $this->imagenProductoPath = null;
        } catch (\Exception $e) {
            // Manejar el error, por ejemplo, registrarlo o mostrar un mensaje
             logger()->error($e->getMessage());
        }
    }

    public function actualizarImagenPrevia(): void
    {
        if ($this->imagen_producto) {
            $this->imagenProductoPath = $this->imagen_producto->temporaryUrl();
        }
    }

    public function eliminarImagenProducto(): void
    {
        $this->imagen_producto = null;
        $this->imagenProductoPath = null;
    }

    public function render(): View
    {
        $atributoProductos = [];
        if ($this->producto && $this->producto->relationLoaded('atributoProductos')) {
            $atributoProductos = $this->producto->atributoProductos
                ->pluck('valor', 'atributo_id')
                ->toArray();
        }

        return view('livewire.producto-form-livewire', [
            'productoEncontrado' => $this->producto,
            'categorias' => $this->categorias,
            'marcas' => $this->marcas,
            'atributoProductos' => $atributoProductos,
        ]);
    }
}
