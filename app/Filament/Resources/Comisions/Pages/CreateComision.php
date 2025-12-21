<?php

namespace App\Filament\Resources\Comisions\Pages;

use App\Filament\Resources\Comisions\ComisionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\ComisionService;
use App\Models\Comision;

class CreateComision extends CreateRecord
{
    protected static string $resource = ComisionResource::class;

    public function generarDatosComision()
    {
        // Los datos están en data.comision_data
        $comisionData = $this->data['comision_data'] ?? [];
        
        \Log::info('Datos del formulario:', $comisionData);
        
        if (!isset($comisionData['vendedor_id']) || !isset($comisionData['periodo_inicial']) || !isset($comisionData['periodo_final'])) {
            \Filament\Notifications\Notification::make()
                ->title('Datos incompletos')
                ->body('Por favor complete el vendedor y los períodos.')
                ->warning()
                ->send();
            return;
        }
        if ($comisionData['periodo_inicial'] > $comisionData['periodo_final']) {
            \Filament\Notifications\Notification::make()
                ->title('Fechas inválidas')
                ->body('La fecha de inicio no puede ser posterior a la fecha final.')
                ->warning()
                ->send();
            return;
        }

        $comisionService = app(ComisionService::class);
        $comision = new Comision();
        $comision->vendedor_id = $comisionData['vendedor_id'];
        $comision->periodo_inicial = $comisionData['periodo_inicial'];
        $comision->periodo_final = $comisionData['periodo_final'];

        // Obtener pedidos y abonos
        $detallesPedidos = $comisionService->obtenerPedidosParaComision($comision);
        $detallesAbonos = $comisionService->obtenerAbonosParaComision($comision);

        // Calcular totales
        $totales = $comisionService->obtenerTotalesComision(
            $comision,
            (float)($comisionData['iva_venta_remisionada'] ?? 0),
            (float)($comisionData['iva_venta_electronica'] ?? 0),
            (float)($comisionData['iva_abonos'] ?? 0),
            (float)($comisionData['porcentaje_comision_ventas'] ?? 0),
            (float)($comisionData['porcentaje_comision_abonos'] ?? 0),
            (float)($comisionData['descuento_comision'] ?? 0),
            (float)($comisionData['ajuste_comision'] ?? 0),
            $detallesPedidos,
            $detallesAbonos
        );

        // Actualizar el estado del formulario
        $this->data['comision_data']['_pedidos_preview'] = $detallesPedidos;
        $this->data['comision_data']['_abonos_preview'] = $detallesAbonos;
        $this->data['comision_data']['_calculados'] = $totales;

        \Filament\Notifications\Notification::make()
            ->title('Datos generados')
            ->body(count($detallesPedidos) . ' pedidos y ' . count($detallesAbonos) . ' abonos encontrados.')
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extraer los datos del componente personalizado
        if (isset($data['comision_data'])) {
            $comisionData = $data['comision_data'];
            
            // Mapear los datos necesarios para crear el registro
            $data['vendedor_id'] = $comisionData['vendedor_id'] ?? null;
            $data['periodo_inicial'] = $comisionData['periodo_inicial'] ?? null;
            $data['periodo_final'] = $comisionData['periodo_final'] ?? null;
            $data['estado_comision'] = $comisionData['estado_comision'] ?? 'PENDIENTE';
            
            // Parámetros editables
            $data['iva_venta_remisionada'] = $comisionData['iva_venta_remisionada'] ?? 0;
            $data['iva_venta_electronica'] = $comisionData['iva_venta_electronica'] ?? 0;
            $data['porcentaje_comision_ventas'] = $comisionData['porcentaje_comision_ventas'] ?? 0;
            $data['iva_abonos'] = $comisionData['iva_abonos'] ?? 0;
            $data['porcentaje_comision_abonos'] = $comisionData['porcentaje_comision_abonos'] ?? 0;
            $data['descuento_comision'] = $comisionData['descuento_comision'] ?? 0;
            $data['ajuste_comision'] = $comisionData['ajuste_comision'] ?? 0;
            
            // Valores calculados (si existen)
            if (isset($comisionData['_calculados'])) {
                $calculados = $comisionData['_calculados'];
                foreach ($calculados as $key => $value) {
                    $data[$key] = $value;
                }
            }
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $comisionData = $this->data['comision_data'] ?? null;
        
        if (!$comisionData) {
            return;
        }

        $comisionService = app(ComisionService::class);
        $record = $this->record;

        // Guardar los detalles (pedidos y abonos)
        if (isset($comisionData['_pedidos_preview'])) {
            $comisionService->agregarPedidosAComision($record, $comisionData['_pedidos_preview']);
        }
        
        if (isset($comisionData['_abonos_preview'])) {
            $comisionService->agregarAbonosAComision($record, $comisionData['_abonos_preview']);
        }
    }
}
