<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    /** @use HasFactory<\Database\Factories\GastoFactory> */
    use HasFactory;

    protected $fillable = [
        'codigo_gasto',
        'concepto_gasto',
        'descripcion_gasto',
        'monto_gasto',
        'fecha_gasto',
        'cuenta_gasto',
        'subcuenta_gasto',
        'comprobante_gasto',
    ];
}
