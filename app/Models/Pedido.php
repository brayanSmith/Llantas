<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Pedido\PedidoCalculoService;
use App\Services\Pedido\PedidoStockService;

class Pedido extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'codigo',
        'fe',
        'cliente_id',
        'fecha',
        'dias_plazo_vencimiento',
        'fecha_vencimiento',
        'ciudad',
        'estado',
        'metodo_pago',
        'tipo_precio',
        'tipo_venta',
        'estado_venta',
        'primer_comentario',
        'segundo_comentario',
        'subtotal',
        'en_cartera',
        'abono',
        'descuento',
        'flete',
        'total_a_pagar',
        'saldo_pendiente',
        'contador_impresiones',
        'estado_vencimiento',
        'impresa',
        'estado_pago',
        'stock_retirado',
        'bodega_id',
        'user_id',
        'alistador_id',
        'imagen_recibido',
        'comentario_entrega',
        'motivo_devolucion',
        'cuenta_total_pedidos_en_cartera',
        'saldo_total_pedidos_en_cartera',
        'fecha_ultimo_abono',
        'estado_cartera',
        'iva',
        'dias_plazo_cartera',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'fecha_ultimo_abono' => 'datetime',
    ];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
    public function abonoPedido()
    {
        return $this->hasMany(Abono::class);
    }

    // Alias para compatibilidad - obtener todos los abonos
    public function abonos()
    {
        return $this->hasMany(Abono::class);
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'bodega_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function alistador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function detallesComision()
    {
        return $this->hasMany(DetalleComisionPedido::class, 'pedido_id');
    }

    public function recalcularTotales()
    {
        $data = PedidoCalculoService::calcularTotalesPedido(
            $this->detalles->toArray(),
            $this->abonos->toArray(),
            $this->descuento ?? 0,
            $this->flete ?? 0
        );
        $this->updateQuietly($data);

        $nuevoEstadoPago = PedidoCalculoService::calcularEstadoPago($this->saldo_pendiente);
        $this->updateQuietly(['estado_pago' => $nuevoEstadoPago]);
    }

    public function setCodigoPedido()
    {
        $nuevoCodigo = PedidoCalculoService::generarCodigoPedido($this->id);
            $this->updateQuietly(['codigo' => $nuevoCodigo]);
    }

    // Atributo: devolver fecha en America/Bogota
    public function getFechaAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        return \Carbon\Carbon::parse($value)->setTimezone('America/Bogota');
    }
}
