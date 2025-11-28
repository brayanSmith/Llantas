<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'codigo',
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
        'imagen_recibido',
        'comentario_entrega',
        'motivo_devolucion',

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
        return $this->hasOne(Abono::class);
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'bodega_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    //Estado de pago basado en campo estado_pago y cálculo de vencimiento para EN_CARTERA
    public function getEstadoPagoFactura()
    {
        $estadoPago = $this->estado_pago;
        
        // Si ya está saldado, retornar directamente
        if ($estadoPago === 'SALDADO') {
            return 'SALDADO';
        }
        
        // Si está EN_CARTERA, calcular si está vencido o al día
        if ($estadoPago === 'EN_CARTERA') {
            $ultimoAbono = $this->abonoPedido()->latest()->first();        
            $diasPlazoVencimiento = $this->dias_plazo_vencimiento;
            $fechaActual = now();
            
            // Calcular fecha de vencimiento basada en último abono o fecha del pedido
            if ($ultimoAbono) {
                $fechaUltimoAbono = $ultimoAbono->fecha;
                $fechaVencimiento = $fechaUltimoAbono->addDays($diasPlazoVencimiento);
            } else {
                $fechaPedido = $this->created_at;
                $fechaVencimiento = $fechaPedido->addDays($diasPlazoVencimiento);
            }
            
            // Comparar fecha actual con vencimiento
            if ($fechaActual->greaterThan($fechaVencimiento)) {
                return 'VENCIDO';
            } else {
                return 'AL_DIA';
            }
        }
        
        // Fallback para otros estados
        return $estadoPago ?? 'INDEFINIDO';
    }
    


     /**
     * 🔹 Recalcula subtotal del pedido
     */
    public function recalcularTotales(): void
    {
        $subtotal = $this->detalles()->sum('subtotal');
        $this->subtotal = $subtotal;
        // Recalcular totales dependientes
        $this->recalcularTotalAPagar();
    }

    
    public function recalcularAbono(): void
    {
        $abonos = $this->abonoPedido()->sum('monto');
        $this->abono = $abonos;
        // Solo recalcular el saldo pendiente (no el total a pagar que es fijo)
        $this->recalcularSaldoPendiente();
    }

    public function aplicarDescuento(float $monto): void
    {
        $this->descuento = $monto;
        // Recalcular tanto el total a pagar como el saldo pendiente
        $this->recalcularTotalAPagar();
    }

    /**
     * Recalcula y guarda el campo `total_a_pagar` (FIJO - no se descuentan abonos).
     *
     * Lógica: total_a_pagar = (subtotal + flete) - descuento (SIN abonos)
     */
    public function recalcularTotalAPagar(): void
    {
        $abonos = $this->abonoPedido()->sum('monto') ?? 0;

        $subtotal = (float) ($this->subtotal ?? 0);
        $flete = (float) ($this->flete ?? 0);
        $descuento = (float) ($this->descuento ?? 0); 

        // Total a Pagar FIJO = (Subtotal + Flete) - Descuento (SIN abonos)
        $totalAPagar = ($subtotal + $flete) - $descuento;
        $this->total_a_pagar = $totalAPagar < 0 ? 0 : $totalAPagar;
        
        // Saldo Pendiente = Total a Pagar - Abonos
        $saldoPendiente = $this->total_a_pagar - $abonos;
        $this->saldo_pendiente = $saldoPendiente < 0 ? 0 : $saldoPendiente;
        
        // Guardamos también el acumulado de abonos en el campo `abono`
        $this->abono = $abonos;
        
        // Actualizar el estado de pago basado en saldo pendiente
        if ($saldoPendiente <= 0) {
            $this->estado_pago = 'SALDADO';
        } else {
            $this->estado_pago = 'EN_CARTERA';
        }

        $this->saveQuietly(); // evita disparar eventos otra vez
    }

    //vamos a cambiar el estado_pago de acuerdo al saldo pendiente
    public function actualizarEstadoPago(): void
    {
        $estadoPago = $this->estado_pago;
        $saldoPendiente = (float) ($this->saldo_pendiente ?? 0);
        if ($saldoPendiente <= 0) {
            $this->estado_pago = 'SALDADO';
        } else {
            $this->estado_pago = 'EN_CARTERA';
        }
        $this->saveQuietly();
    }

    /**
     * Recalcula solo el saldo pendiente (cuando ya existe el total_a_pagar)
     */
    public function recalcularSaldoPendiente(): void
    {
        $abonos = $this->abonoPedido()->sum('monto') ?? 0;
        $saldoPendiente = $this->total_a_pagar - $abonos;
        $this->saldo_pendiente = $saldoPendiente < 0 ? 0 : $saldoPendiente;
        $this->abono = $abonos;
        $this->saveQuietly();
    }

     /**
     * The "booted" method of the model.
     *
     * Here we set the 'codigo' field after the model is created.
     */


    protected static function booted()
    {
        static::created(function ($pedido) {
            $pedido->codigo = 'PED-' . $pedido->id;
            $pedido->saveQuietly();
        });
    }

    // Atributo: devolver fecha en America/Bogota
    public function getFechaAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        return \Carbon\Carbon::parse($value)->setTimezone('America/Bogota');
    }

    /**
     * Accessor para obtener el estado de pago de la factura
     */
    public function getEstadoPagoFacturaAttribute(): string
    {
        // Si no hay saldo pendiente o es 0, está saldada
        $saldoPendiente = (float) ($this->saldo_pendiente ?? 0);
        if ($saldoPendiente <= 0) {
            return 'SALDADA';
        }

        // Si hay saldo pendiente, verificar si está vencida
        if ($this->fecha_vencimiento) {
            $hoy = \Carbon\Carbon::now()->startOfDay();
            $fechaVencimiento = \Carbon\Carbon::parse($this->fecha_vencimiento)->startOfDay();
            
            if ($fechaVencimiento->isPast()) {
                return 'VENCIDA';
            }
        }

        // Si tiene saldo pero no está vencida, está al día
        return 'AL DIA';
    }
}
