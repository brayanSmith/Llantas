<?php

namespace App\Filament\Resources\Comisions\Pages;

use App\Filament\Resources\Comisions\ComisionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;


class EditComision extends EditRecord
{
    protected static string $resource = ComisionResource::class;

    public function generarDatosComision()
    {
        // Los datos están en data.comision_data
        $comisionData = $this->data['comision_data'] ?? [];
        
        if (!isset($comisionData['vendedor_id']) || !isset($comisionData['periodo_inicial']) || !isset($comisionData['periodo_final'])) {
            \Filament\Notifications\Notification::make()
                ->title('Datos incompletos')
                ->body('Por favor complete el vendedor y los períodos.')
                ->warning()
                ->send();
            return;
        }

        $comisionService = app(\App\Services\ComisionService::class);
        $comision = $this->record;
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

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extraer los datos del componente personalizado
        if (isset($data['comision_data'])) {
            $comisionData = $data['comision_data'];
            
            // Mapear los datos necesarios para actualizar el registro
            $data['vendedor_id'] = $comisionData['vendedor_id'] ?? $this->record->vendedor_id;
            $data['periodo_inicial'] = $comisionData['periodo_inicial'] ?? $this->record->periodo_inicial;
            $data['periodo_final'] = $comisionData['periodo_final'] ?? $this->record->periodo_final;
            $data['estado_comision'] = $comisionData['estado_comision'] ?? $this->record->estado_comision;
            
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

    protected function afterSave(): void
    {
        $comisionData = $this->data['comision_data'] ?? null;
        
        if (!$comisionData) {
            return;
        }

        $comisionService = app(\App\Services\ComisionService::class);
        $record = $this->record;

        // Limpiar detalles existentes antes de agregar nuevos
        $record->detallesComisionPedidos()->delete();
        $record->detallesComisionAbonos()->delete();

        // Guardar los detalles (pedidos y abonos)
        if (isset($comisionData['_pedidos_preview'])) {
            $comisionService->agregarPedidosAComision($record, $comisionData['_pedidos_preview']);
        }
        
        if (isset($comisionData['_abonos_preview'])) {
            $comisionService->agregarAbonosAComision($record, $comisionData['_abonos_preview']);
        }
    }
}
