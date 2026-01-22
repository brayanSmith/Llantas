<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Services\CompraCalculoService;

class Compra extends Model
{
    /** @use HasFactory<\Database\Factories\CompraFactory> */
    use HasFactory;
    protected $fillable = [
        'factura',
        'proveedor_id',
        'fecha',
        'dias_plazo_vencimiento',
        'fecha_vencimiento',
        'metodo_pago',
        'estado_pago',
        'tipo_compra',
        'estado',
        'observaciones',
        'subtotal',
        'abono',
        'descuento',
        'total_a_pagar',
        'categoria_compra',
        'item_compra',
        'bodega_id',
        'saldo_pendiente',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    protected function titulo(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->id} - {$this->factura}",
        );
    }
    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class);
    }
    public function abonoCompra()
    {
        return $this->hasMany(AbonoCompra::class);
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'bodega_id');
    }
    public function recalcularTotales()
    {
        $data = CompraCalculoService::calcular(
            $this->detallesCompra->toArray(),
            $this->abonoCompra->toArray(),
            $this->descuento ?? 0
        );
        $this->update($data);
    }

    public function setEstadoPago(){
        $nuevoEstadoPago = CompraCalculoService::calcularEstadoPago((float) ($this->saldo_pendiente ?? 0));
        if (($this->estado_pago ?? null) !== $nuevoEstadoPago) {
            $this->updateQuietly(['estado_pago' => $nuevoEstadoPago]);
        }
    }

    public function getFechaRecibidoCompra($value)
    {
        if (is_null($value)){
            return null;
        }
        return \Carbon\Carbon::parse($value)->setTimezone('America/Bogota');
    }
}
