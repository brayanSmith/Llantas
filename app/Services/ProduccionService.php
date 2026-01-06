<?php

namespace App\Services;

use App\Models\Produccion;
use App\Models\DetalleProduccionEntrada;
use App\Models\DetalleProduccionSalida;
use App\Services\StockCalculoService;
use Illuminate\Support\Facades\DB;

class ProduccionService
{
    /**
     * Agregar detalles de producción de salida basados en la fórmula
     *
     * @param Produccion $produccion
     * @return void
     */
    public function agregarDetalleProduccionSalida(Produccion $produccion)
    {
        // Obtener la fórmula asociada a la producción
        $formula = $produccion->formula;

        if (!$formula) {
            throw new \Exception('No se encontró la fórmula asociada a la producción');
        }

        // Obtener los detalles de la fórmula
        $detallesFormula = $formula->detalleFormulas;

        if ($detallesFormula->isEmpty()) {
            throw new \Exception('La fórmula no tiene productos asociados');
        }

        // Recorrer cada detalle de la fórmula y crear el registro de salida
        foreach ($detallesFormula as $detalleFormula) {
            // Calcular la cantidad basada en la cantidad de producción
            $cantidadSalida = $detalleFormula->cantidad_producto * $produccion->cantidad;

            // Crear el registro de detalle de producción salida
            DetalleProduccionSalida::create([
                'produccion_id' => $produccion->id,
                'producto_id' => $detalleFormula->producto_id,
                'cantidad_producto' => $cantidadSalida,
                'costo_producto' => $detalleFormula->producto->costo_producto,
                'total_costo' => $cantidadSalida * $detalleFormula->producto->costo_producto,
                'fecha_produccion' => $produccion->fecha_produccion,
            ]);
        }
    }


}
