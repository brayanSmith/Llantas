<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'categoria_id',
        'marca_id',
        'referencia_producto',
        'descripcion_producto',
        'costo_producto',
        'valor_detal',
        'valor_mayorista',
        'valor_sin_instalacion',
        'imagen_producto',
        'concatenar_codigo_nombre',

    ];
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function traslados()
    {
        return $this->hasMany(Traslado::class);
    }

    public function stockBodegas()
    {
        return $this->hasMany(StockBodega::class);
    }

    public function atributoProductos()
    {
        return $this->hasMany(AtributoProducto::class, 'producto_id');
    }

    public function enStock(float|int $cantidad): bool
    {
        // cantidad inválida
        if ($cantidad <= 0) {
            return false;
        }

        $stock = (float) ($this->stock ?? 0);

        return $stock >= (float) $cantidad;
    }

}
