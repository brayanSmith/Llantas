<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    //
    protected $fillable = [
        'categoria_id',
        'nombre',
        'tipo',
        'opciones',
        'valor_por_defecto',
    ];
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    public function atributoProducto()
    {
        return $this->hasMany(AtributoProducto::class, 'atributo_id');
    }
}
