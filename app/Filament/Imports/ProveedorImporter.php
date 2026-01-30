<?php

namespace App\Filament\Imports;

use App\Models\Proveedor;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProveedorImporter extends Importer
{
    protected static ?string $model = Proveedor::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nombre_proveedor')
                ->example('Proveedor Ejemplo S.A.S')
                ->validationAttribute('Nombre del proveedor inválido')
                ->rules(['required', 'max:255']),
            ImportColumn::make('razon_social_proveedor')
                ->example('Proveedor Ejemplo S.A.S')
                ->validationAttribute('Razón social del proveedor inválida')
                ->rules(['max:255']),
            ImportColumn::make('nit_proveedor')
                ->example('900123456-7')
                ->validationAttribute('NIT del proveedor inválido')
                ->rules(['required', 'max:255', 'unique:proveedors,nit_proveedor']),
            ImportColumn::make('ciudad_nit_proveedor')
                ->example('Bogotá')
                ->validationAttribute('Ciudad del NIT del proveedor inválida')
                ->rules(['required', 'max:255']),
            ImportColumn::make('tipo_proveedor')
                ->example('REMISIONADO')
                ->validationAttribute('Tipo de proveedor inválido solo se acepta REMISIONADO o ELECTRONICO')
                ->rules(['nullable', 'in:REMISIONADO,ELECTRONICO']),
            ImportColumn::make('categoria_proveedor')
                ->example('NO_DECLARANTE')
                ->validationAttribute('Categoría del proveedor inválida solo se acepta DECLARANTE, NO_DECLARANTE o RETENEDOR')
                ->rules(['nullable', 'in:DECLARANTE,NO_DECLARANTE,RETENEDOR']),
            ImportColumn::make('convenio')
                ->example('CONV12345')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('departamento_proveedor')
                ->example('Cundinamarca')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('ciudad_proveedor')
                ->example('Bogotá')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('direccion_proveedor')
                ->example('Calle 123 #45-67')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('telefono_proveedor')
                ->example('3001234567')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('banco_proveedor')
                ->example('Banco Ejemplo')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('tipo_cuenta_proveedor')
                ->example('AHORRO')
                ->validationAttribute('Tipo de cuenta del proveedor inválida solo se acepta AHORRO o CORRIENTE')
                ->rules(['nullable', 'in:AHORRO,CORRIENTE']),
            ImportColumn::make('numero_cuenta_proveedor')
                ->example('1234567890')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('tiempo_respuesta')
                ->example('48 horas')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('fabricante')
                ->example('Fabricante Ejemplo')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('flete')
                ->example('1')
                ->validationAttribute('Flete inválido, debe ser 1 (true) o 0 (false)')
                ->boolean()
                ->rules(['nullable', 'boolean']),
            ImportColumn::make('valor_flete')
                ->example('15000.50')
                ->validationAttribute('Valor del flete inválido')
                ->rules(['nullable', 'numeric']),
        ];
    }

    public function resolveRecord(): Proveedor
    {
        return Proveedor::firstOrNew([
            'nit_proveedor' => $this->data['nit_proveedor'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your proveedor import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
