<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleProduccionSalida extends Model
{
    //
    protected $table = 'detalle_produccion_salidas';
    protected $fillable = [
        'produccion_id',
        'producto_id',
        'cantidad_producto',
        'fecha_produccion',
        'observaciones',
        'lote',
    ];
    public function produccion()
    {
        return $this->belongsTo(Produccion::class, 'produccion_id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
