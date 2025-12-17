<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'formula_id',
        'cantidad',
        'lote',
        'fecha_produccion',
        'fecha_caducidad',
        'observaciones',        
    ];
    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }
    public function detalleProducciones()
    {
        return $this->hasMany(DetalleProduccion::class);
    }

}
