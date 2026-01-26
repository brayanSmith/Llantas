<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Repeater;
use App\Models\Pedido;
use App\Models\Producto;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;

class RepeaterPedido extends Repeater
{
    protected string $view = 'filament.forms.components.repeater-pedido';

    public static function make(?string $name = null): static
    {
        // Ahora el schema está vacío para aceptar el array generado por Alpine.js desde el Blade
        return parent::make($name)
            ->schema([]);
    }

    public function products($productos = null)
    {
        $productos = $productos ??
        \App\Models\Producto::where('categoria_producto', 'PRODUCTO_TERMINADO')
            ->get([
                'id',
                'nombre_producto',
                'codigo_producto',
                'concatenar_codigo_nombre',
                'valor_detal_producto',
                'valor_mayorista_producto',
                'valor_ferretero_producto',
                'iva_producto',
            ]);
        return $this->meta('products', $productos->map(function($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre_producto,
                'codigo' => $p->codigo_producto,
                'concatenarCodigoNombre' => $p->concatenar_codigo_nombre,
                'valorDetal' => $p->valor_detal_producto,
                'valorMayorista' => $p->valor_mayorista_producto,
                'valorFerretero' => $p->valor_ferretero_producto,
                'iva' => $p->iva_producto,
            ];
        })->values());
    }
}
