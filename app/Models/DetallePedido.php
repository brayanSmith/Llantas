<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;
use App\Models\Producto;

class DetallePedido extends Model
{
    //
    use HasFactory;

    protected $table = 'detalle_pedidos';
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'aplicar_iva',
        'iva',
        'subtotal'
    ];
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

     /**
     * 🔹 Recalcula el subtotal del detalle
     */
    public function recalcularSubtotal(): void 
    {
        $cantidad = $this->cantidad ?? 0;
        $precioUnitario = $this->precio_unitario ?? 0;
        $aplicarIva = $this->aplicar_iva ?? false;
        
        // Obtener el IVA del producto relacionado
        $ivaPercentage = 0;
        if ($this->producto_id) {
            $producto = \App\Models\Producto::find($this->producto_id);
            $ivaPercentage = $producto?->iva_producto ?? 0;
            // Actualizar el campo iva del detalle con el IVA del producto
            $this->iva = $ivaPercentage;
        }
        
        // Solo aplicar IVA si la casilla está marcada
        $factorIva = 1;
        if ($aplicarIva && $ivaPercentage > 0) {
            $factorIva = ($ivaPercentage / 100) + 1;
        }
        
        // Subtotal = Cantidad * (Precio unitario * factor IVA)
        $this->subtotal = $cantidad * ($precioUnitario * $factorIva);

        // importante: no dispares eventos infinitos
        $this->saveQuietly();
    }

    protected static function booted()
    {
        // cada vez que se cree o actualice un detalle
        static::saving(function (DetallePedido $detalle) {
            $cantidad = $detalle->cantidad ?? 0;
            $precioUnitario = $detalle->precio_unitario ?? 0;
            $aplicarIva = $detalle->aplicar_iva ?? false;
            
            // Obtener el IVA del producto relacionado
            $ivaPercentage = 0;
            if ($detalle->producto_id) {
                $producto = \App\Models\Producto::find($detalle->producto_id);
                $ivaPercentage = $producto?->iva_producto ?? 0;
                // Actualizar el campo iva del detalle con el IVA del producto
                $detalle->iva = $ivaPercentage;
            }
            
            // Solo aplicar IVA si la casilla está marcada
            $factorIva = 1;
            if ($aplicarIva && $ivaPercentage > 0) {
                $factorIva = ($ivaPercentage / 100) + 1;
            }
            
            // Subtotal = Cantidad * (Precio unitario * factor IVA)
            $detalle->subtotal = $cantidad * ($precioUnitario * $factorIva);
        });

        static::saved(function (DetallePedido $detalle) {
            // actualiza el pedido padre
            $detalle->pedido?->recalcularTotales();
        });

        static::deleted(function (DetallePedido $detalle) {
            // también al borrar, recalculamos el pedido
            $detalle->pedido?->recalcularTotales();
        });
    }
}

