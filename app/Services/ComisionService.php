<?php

namespace App\Services;

use App\Models\Comision;

class ComisionService
{
    //vamos a crear una funcion que agregue al detalle comision los pedidos que que correspondan a la fecha del comision a la fecha final de la comision y que sean las ventas del vendedor
    // Retorna los datos sin guardar para previsualización
    public function obtenerPedidosParaComision(Comision $comision)
    {
        $pedidos = $comision->vendedor->pedidos()
            ->whereBetween('fecha', [$comision->periodo_inicial, $comision->periodo_final])
            ->whereIn('estado', ['FACTURADO', 'ENTREGADO'])
            ->get();

        $detalles = [];
        foreach ($pedidos as $pedido) {
            $detalles[] = [
                'pedido_id' => $pedido->id,
                'monto_venta' => $pedido->total_a_pagar,
                'fecha_venta' => $pedido->fecha,
                'tipo_venta' => $pedido->tipo_venta,
                'fecha_actualizacion_venta' => $pedido->updated_at,
            ];
        }

        return $detalles;
    }

    // Retorna los datos sin guardar para previsualización
    public function obtenerAbonosParaComision(Comision $comision)
    {
        $abonos = $comision->vendedor->abonosVendedor()
            ->whereBetween('fecha', [$comision->periodo_inicial, $comision->periodo_final])            
            ->get();

        $detalles = [];
        foreach ($abonos as $abono) {
            $detalles[] = [
                'abono_id' => $abono->id,
                'monto_abono' => $abono->monto,
                'fecha_abono' => $abono->fecha,
                'fecha_actualizacion_abono' => $abono->updated_at,
            ];
        }

        return $detalles;
    }

    // Guarda los pedidos en la base de datos
    public function agregarPedidosAComision(Comision $comision, array $detallesPedidos = null)
    {
        $detalles = $detallesPedidos ?? $this->obtenerPedidosParaComision($comision);

        foreach ($detalles as $detalle) {
            $comision->detallesComisionPedidos()->create($detalle);
        }
    }

    // Guarda los abonos en la base de datos
    public function agregarAbonosAComision(Comision $comision, array $detallesAbonos = null)
    {
        $detalles = $detallesAbonos ?? $this->obtenerAbonosParaComision($comision);

        foreach ($detalles as $detalle) {
            $comision->detallesComisionAbonos()->create($detalle);
        }
    }

    // Calcula los totales sin guardar, puede usar datos temporales o de BD
    public function obtenerTotalesComision(
        Comision $comision,
        float $ivaVentasRemisionadas, 
        float $ivaVentasElectronica,
        float $ivaAbonos,
        float $porcentajeComisionVentas, 
        float $porcentajeComisionAbonos,
        float $descuentoComision = 0, 
        float $ajusteComision = 0,
        array $detallesPedidos = null,
        array $detallesAbonos = null)
    {
        // Si se pasan arrays temporales, usarlos; sino consultar BD
        if ($detallesPedidos !== null) {
            $montoVentasRemisionadas = collect($detallesPedidos)
                ->where('tipo_venta', 'REMISIONADA')
                ->sum('monto_venta');
            
            $montoVentasElectronica = collect($detallesPedidos)
                ->where('tipo_venta', 'ELECTRONICA')
                ->sum('monto_venta');
        } else {
            $montoVentasRemisionadas = $comision->detallesComisionPedidos()
                ->where('tipo_venta', 'REMISIONADA')
                ->sum('monto_venta');       

            $montoVentasElectronica = $comision->detallesComisionPedidos()
                ->where('tipo_venta', 'ELECTRONICA')
                ->sum('monto_venta');
        }

        if ($detallesAbonos !== null) {
            $montoAbonos = collect($detallesAbonos)->sum('monto_abono');
        } else {
            $montoAbonos = $comision->detallesComisionAbonos()->sum('monto_abono');
        }

        $totalVentasRemisionadas = $montoVentasRemisionadas * (1 + $ivaVentasRemisionadas / 100);
        $totalVentasElectronica = $montoVentasElectronica * (1 + $ivaVentasElectronica / 100);

        $totalVentas = $montoVentasRemisionadas + $montoVentasElectronica;
        $totalComisionVentas = $totalVentas * ($porcentajeComisionVentas / 100);
        
        $totalAbonos = $montoAbonos * (1 + $ivaAbonos / 100);
        $totalComisionAbonos = $totalAbonos * ($porcentajeComisionAbonos / 100);
        $subtotalComision = $totalComisionVentas + $totalComisionAbonos;
        $totalComisionNeta = $subtotalComision - $descuentoComision + $ajusteComision;

        return [
            'monto_venta_remisionada' => $montoVentasRemisionadas,
            'iva_venta_remisionada' => $ivaVentasRemisionadas,
            'total_venta_remisionada' => $totalVentasRemisionadas,
            
            'monto_venta_electronica' => $montoVentasElectronica,
            'iva_venta_electronica' => $ivaVentasElectronica,
            'total_venta_electronica' => $totalVentasElectronica,
            
            'monto_total_ventas' => $totalVentas,
            'porcentaje_comision_ventas' => $porcentajeComisionVentas,
            'total_comision_ventas' => $totalComisionVentas,
            
            'monto_abonos' => $montoAbonos,
            'iva_abonos' => $ivaAbonos,
            'total_abonos' => $totalAbonos,
            'porcentaje_comision_abonos' => $porcentajeComisionAbonos,
            'total_comision_abonos' => $totalComisionAbonos,
            
            'subtotal_comision' => $subtotalComision,
            'descuento_comision' => $descuentoComision,
            'ajuste_comision' => $ajusteComision,
            'total_comision_neta' => $totalComisionNeta,
        ];
    }

    // Guarda los totales en la base de datos
    public function calcularTotalesComision(
        Comision $comision, 
        float $ivaVentasRemisionadas, 
        float $ivaVentasElectronica,
        float $ivaAbonos,
        float $porcentajeComisionVentas, 
        float $porcentajeComisionAbonos,
        float $descuentoComision = 0, 
        float $ajusteComision = 0,
        array $totales = null)
    {
        // Si no se pasan totales calculados, calcularlos
        $totales = $totales ?? $this->obtenerTotalesComision(
            $comision,
            $ivaVentasRemisionadas,
            $ivaVentasElectronica,
            $ivaAbonos,
            $porcentajeComisionVentas,
            $porcentajeComisionAbonos,
            $descuentoComision,
            $ajusteComision
        );

        // Asignar todos los valores
        foreach ($totales as $campo => $valor) {
            $comision->$campo = $valor;
        }
        
        $comision->save();
    }
}