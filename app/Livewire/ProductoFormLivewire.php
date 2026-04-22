<?php

namespace App\Livewire;

use App\Models\Marca;
use App\Models\Producto;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ProductoFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithFileUploads;

    public ?Producto $producto = null;
    public ?array $marca = [];
    public ?array $marcas = [];
    public bool $esEdicion = false;

        public $imagen_producto = null;
    public ?string $imagenProductoPath = null;

    protected function getProductoDefaults(): array
    {
        return [
                'categoria' => '',
                'tipo' => '',

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
            $productoEncontrado = Producto::with(['marca'])->find($productoId);
            if ($productoEncontrado) {
                $this->producto = $productoEncontrado;
                $this->marca = $productoEncontrado->marca ? $productoEncontrado->marca->toArray() : [];

                // Cargar path de imagen si existe
                if ($productoEncontrado->imagen_producto) {
                    $this->imagenProductoPath = Storage::disk('public')->url($productoEncontrado->imagen_producto);
                }
            }
        }
        // Cargar todas las categorías y marcas disponibles
        $this->marcas = Marca::get()->toArray();
    }

    public function guardarProducto($datos): void
    {
        try {
            $productoData = $datos;

            // Guardar la imagen si existe
            $rutaImagen = null;
            if ($this->imagen_producto) {
                $rutaImagen = $this->imagen_producto->store('productos', 'public');
                $productoData['imagen_producto'] = $rutaImagen;
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
        return view('livewire.producto-form-livewire', [
            'productoEncontrado' => $this->producto,
            'marcas' => $this->marcas,
        ]);
    }
}
