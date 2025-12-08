<?php

namespace App\Services;

class CompraCalculoService
{
    //
    public static function calcular(array $detalles, array $abonos, float $descuento){
        $subtotal = collect($detalles)->sum(function($item){
            return ($item['cantidad'] ?? 0) * ($item['precio_unitario'] ?? 0);
        });
        $abonos = collect($abonos)->sum(function($item){
            return $item['monto'] ?? 0;
        });

        $total = $subtotal - $descuento - $abonos;
        return [
            'subtotal' => $subtotal,
            'abono' => $abonos,
            'total_a_pagar' => $total,
        ];


    }
}