<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\PedidoCalculoService;
use App\Services\PedidoStockService;

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

    ];

    protected $casts = [
        'fecha' => 'datetime',

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
        // Obtener todos los abonos del pedido
        $abonosArray = $this->abonos()->get()->toArray();

        $data = PedidoCalculoService::calcularTotalesPedido(
            $this->detalles->toArray(),
            $abonosArray,
            $this->descuento ?? 0,
            $this->flete ?? 0
        );

        // Usar updateQuietly para evitar disparar el observer y crear un loop infinito
        $this->updateQuietly($data);
    }

    public function setEstadoPago(){
        $nuevoEstadoPago = PedidoCalculoService::calcularEstadoPago((float) ($this->saldo_pendiente ?? 0));
        if (($this->estado_pago ?? null) !== $nuevoEstadoPago) {
            $this->updateQuietly(['estado_pago' => $nuevoEstadoPago]);
        }
    }

    public function setEstadoVencimiento()
    {
        $nuevoEstadoVencimiento = PedidoCalculoService::calcularEstadoVencimiento($this);
            $this->updateQuietly(['estado_vencimiento' => $nuevoEstadoVencimiento]);
    }

    public function setCodigoPedido()
    {
        $nuevoCodigo = PedidoCalculoService::generarCodigoPedido($this->id);
            $this->updateQuietly(['codigo' => $nuevoCodigo]);
    }

    /**
     * Obtener el estado de pago formateado para mostrar
     */
    /*public function getEstadoPagoFactura(): string
    {
        return match($this->estado_pago) {
            'SALDADO' => 'Pagado',
            'EN_CARTERA' => 'Pendiente',
            default => $this->estado_pago ?? 'Desconocido'
        };
    }*/


    // Atributo: devolver fecha en America/Bogota
    public function getFechaAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        return \Carbon\Carbon::parse($value)->setTimezone('America/Bogota');
    }
}
