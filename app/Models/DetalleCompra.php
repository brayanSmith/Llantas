<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\CompraCalculoService;

class DetalleCompra extends Model
{
    /** @use HasFactory<\Database\Factories\DetalleCompraFactory> */
    use HasFactory;
    protected $fillable = [
        'compra_id',
        //'producto_id',
        'item_id',
        'descripcion_item',
        'cantidad',
        'precio_unitario',
        'iva',
        'precio_con_iva',
        'subtotal',
        'tipo_item',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'item_id');
    }

    public function puc()
    {
        return $this->belongsTo(Puc::class, 'item_id');
    }
   public function recalcularSubtotal()
    {
        $resultado = CompraCalculoService::calcularDetalles([
            'item_id' => $this->item_id,
            'descripcion_item' => $this->descripcion_item,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'iva' => $this->iva,
        ]);
        $this->update([
            'subtotal' => $resultado['subtotal'],
            'precio_con_iva' => $resultado['precio_con_iva']
        ]);
    }
}
