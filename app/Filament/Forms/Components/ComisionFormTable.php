<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use App\Services\ComisionService;
use App\Models\Comision;

class ComisionFormTable extends Field
{
    protected string $view = 'filament.forms.components.comision-form-table';

    protected function setUp(): void
    {
        parent::setUp();

        // Inicializar con valores por defecto
        $this->default([
            'vendedor_id' => null,
            'periodo_inicial' => null,
            'periodo_final' => null,
            'estado_comision' => 'PENDIENTE',
            'iva_venta_remisionada' => 19,
            'iva_venta_electronica' => 19,
            'porcentaje_comision_ventas' => 1,
            'iva_abonos' => 19,
            'porcentaje_comision_abonos' => 1.5,
            'descuento_comision' => 0,
            'ajuste_comision' => 0,
        ]);

        $this->dehydrateStateUsing(function ($state) {
            // Retornar el estado completo para que las páginas puedan procesarlo
            return $state;
        });
        
        // Recalcular solo totales cuando cambian los valores editables (IVA, porcentajes, etc)
        $this->afterStateUpdated(function ($state, $set, $livewire) {
            $this->recalcularTotales($state, $set, $livewire);
        });
        
        // Marcar como live para que reaccione a cambios en los campos editables
        $this->live(onBlur: false);
    }

    protected function recalcularTotales($state, $set, $livewire): void
    {
        $comisionService = app(ComisionService::class);
        
        // Solo recalcular totales si ya tenemos pedidos y abonos
        if (!isset($state['_pedidos_preview']) || !isset($state['_abonos_preview'])) {
            return;
        }

        $record = $livewire->getRecord();
        $comisionTemp = $record ?? new Comision();
        $comisionTemp->iva_venta_remisionada = $state['iva_venta_remisionada'] ?? 0;
        $comisionTemp->iva_venta_electronica = $state['iva_venta_electronica'] ?? 0;
        $comisionTemp->porcentaje_comision_ventas = $state['porcentaje_comision_ventas'] ?? 0;
        $comisionTemp->iva_abonos = $state['iva_abonos'] ?? 0;
        $comisionTemp->porcentaje_comision_abonos = $state['porcentaje_comision_abonos'] ?? 0;
        $comisionTemp->descuento_comision = $state['descuento_comision'] ?? 0;
        $comisionTemp->ajuste_comision = $state['ajuste_comision'] ?? 0;

        // Calcular totales con los datos existentes
        $totales = $comisionService->obtenerTotalesComision(
            $comisionTemp,
            (float)($state['iva_venta_remisionada'] ?? 0),
            (float)($state['iva_venta_electronica'] ?? 0),
            (float)($state['iva_abonos'] ?? 0),
            (float)($state['porcentaje_comision_ventas'] ?? 0),
            (float)($state['porcentaje_comision_abonos'] ?? 0),
            (float)($state['descuento_comision'] ?? 0),
            (float)($state['ajuste_comision'] ?? 0),
            $state['_pedidos_preview'],
            $state['_abonos_preview']
        );

        $state['_calculados'] = $totales;
        $set($this->getName(), $state);
    }
}
