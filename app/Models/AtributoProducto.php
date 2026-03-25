<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtributoProducto extends Model
{
    //
    protected $fillable = [
        'producto_id',
        'atributo_id',
        'valor',
    ];
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'atributo_id');
    }
}
