<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    /** @use HasFactory<\Database\Factories\GastoFactory> */
    use HasFactory;

    protected $fillable = [

        'descripcion',
        'monto',
        'fecha_gasto',
    ];
}
