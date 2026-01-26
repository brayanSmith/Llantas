<?php

namespace App\Livewire;

use App\Models\Pedido;
use Livewire\Component;
use App\Models\Producto;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;

class LivewireRepeaterPedido extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    //public Pedido $record;

    public ?array $data = [];
    public array $productos = [];
    public array $items = [];
    public ?int $pedido_id = null;

    public function mount(): void
    {
        $this->productos = Producto::where('categoria_producto', 'PRODUCTO_TERMINADO')->get([
            'id',
            'nombre_producto',
            'codigo_producto',
            'concatenar_codigo_nombre',
            'valor_detal_producto',
            'valor_mayorista_producto',
            'valor_ferretero_producto',
            'iva_producto',
        ])->map(function ($producto) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre_producto,
                'codigo' => $producto->codigo_producto,
                'concatenarCodigoNombre' => $producto->concatenar_codigo_nombre,
                'valorDetal' => $producto->valor_detal_producto,
                'valorMayorista' => $producto->valor_mayorista_producto,
                'valorFerretero' => $producto->valor_ferretero_producto,
                'iva' => $producto->iva_producto,
            ];
        })->toArray();
    }


    /*public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ])
            ->statePath('data')
            ->model($this->record);
    }
*/
    public function save(): void
    {
        if (!$this->pedido_id) {
            // No hay pedido asociado, no guardar
            return;
        }
        foreach ($this->items as $item) {
            \App\Models\DetallePedido::create([
                'pedido_id' => $this->pedido_id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_manual'] ?? $item['precio_sugerido'] ?? 0,
                'aplicar_iva' => $item['incluir_iva'] ?? false,
                'iva' => $item['iva'] ?? 0,
                'subtotal' => $item['subtotal'] ?? 0,
                'precio_con_iva' => $item['precio_con_iva'] ?? 0,
            ]);
        }
    }

    // Este método transforma los datos recibidos del frontend para integrarlos a la relación detalles
    public function getDetallesForSave(): array
    {
        return collect($this->items)
            ->map(function ($item) {
                return [
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_manual'] ?? $item['precio_sugerido'] ?? 0,
                    'aplicar_iva' => $item['incluir_iva'] ?? false,
                    'iva' => $item['iva'] ?? 0,
                    'subtotal' => $item['subtotal'] ?? 0,
                    'precio_con_iva' => $item['precio_con_iva'] ?? 0,
                ];
            })
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.livewire-repeater-pedido', [
            'productos' => $this->productos,
        ]);
    }
}
