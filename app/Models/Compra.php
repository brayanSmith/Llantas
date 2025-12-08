<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
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
    
    public function getFechaRecibidoCompra($value)
    {
        if (is_null($value)){
            return null;
        }
        return \Carbon\Carbon::parse($value)->setTimezone('America/Bogota');
    }
}
