<?php

namespace App\Livewire;

use App\Models\Atributo;
use App\Models\Categoria;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CategoriaFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $categoria = [];
    public ?array $atributos = [];
    public bool $esEdicion = false;

    protected function getCategoriaDefaults(): array
    {
        return [
            'nombre_categoria' => '',
        ];
    }

    protected function getAtributoDefaults(): array
    {
        return [
            'nombre' => '',
            'tipo' => 'TEXTO',
            'opciones' => [],
            'valor_por_defecto' => '',
        ];
    }

    public function mount(): void
    {
        // Intenta obtener el ID desde la ruta primero (para Filament)
        $categoriaId = request()->route('record');

        // Si no está en la ruta, intenta desde query string
        if (!$categoriaId) {
            $categoriaId = request()->get('categoria_id');
        }

        // Convierte a ID si es un modelo
        if ($categoriaId && is_object($categoriaId)) {
            $categoriaId = $categoriaId->id;
        }

        $this->categoria = $this->getCategoriaDefaults();
        if ($categoriaId) {
            $this->esEdicion = true;
            $categoriaEncontrada = Categoria::with('atributos')->find($categoriaId);
            if ($categoriaEncontrada) {
                $this->categoria = $categoriaEncontrada->toArray();
                $this->atributos = $categoriaEncontrada->atributos->toArray();
            }
        }
    }

    public function guardarCategoria($datos): void
    {
        try {
            $categoria = $datos;
            $atributos = $datos['atributos'] ?? [];

            // Guardar o actualizar categoría
            if(isset($categoria['id']) && $this->esEdicion){
                $categoriaModel = Categoria::find($categoria['id']);
                $categoriaModel->update(['nombre_categoria' => $categoria['nombre_categoria']]);
            } else {
                $categoriaModel = Categoria::create(['nombre_categoria' => $categoria['nombre_categoria']]);
            }

            // Eliminar atributos anteriores si es edición
            if($this->esEdicion && isset($categoria['id'])) {
                $categoriaModel->atributos()->delete();
            }

            // Guardar nuevos atributos
            if(is_array($atributos) && count($atributos) > 0) {
                foreach ($atributos as $atributoData) {
                    if(!empty($atributoData['nombre'])) {
                        $opciones = null;
                        if($atributoData['tipo'] === 'ENUM' && !empty($atributoData['opciones'])) {
                            $opciones = explode(',', $atributoData['opciones']);
                            $opciones = array_map('trim', $opciones);
                        }

                        Atributo::create([
                            'categoria_id' => $categoriaModel->id,
                            'nombre' => $atributoData['nombre'],
                            'tipo' => $atributoData['tipo'],
                            'opciones' => $opciones ? json_encode($opciones) : null,
                            'valor_por_defecto' => $atributoData['valor_por_defecto'] ?? null,
                        ]);
                    }
                }
            }

            $this->dispatch('notify-success');
        } catch (\Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.categoria-form-livewire',[
                'categoriaEncontrada' => $this->categoria,
                'atributos' => $this->atributos,
        ]);
    }
}
