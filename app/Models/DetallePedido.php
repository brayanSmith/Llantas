<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\Pedido\PedidoCalculoService;

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
        'precio_con_iva',
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
    public function getProductoDatos()
    {
        return PedidoCalculoService::calcularDatosProducto($this->producto);
    }

    public function recalcularSubtotal()
    {
        $resultado = PedidoCalculoService::calcularDetalles([
            'producto_id' => $this->producto_id,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'aplicar_iva' => $this->aplicar_iva,
            'iva' => $this->iva,
        ]);
        $this->update([
            'subtotal' => $resultado['subtotal'],
            'precio_con_iva' => $resultado['precio_con_iva']
        ]);
    }

}

