<?php

namespace App\Filament\Imports;

use App\Models\Cliente;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use App\Models\Ruta;
use App\Models\User;

class ClienteImporter extends Importer
{
    protected static ?string $model = Cliente::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('tipo_documento')
                ->example('CC') 
                ->validationAttribute('Tipo de documento inválido')               
                ->rules(['nullable', 'max:255', 'required']),
            ImportColumn::make('numero_documento')
                ->example('1234567890', 'unique')
                ->validationAttribute('Número de documento inválido')
                ->rules(['nullable', 'max:255', 'required', 'unique:clientes,numero_documento']),
            ImportColumn::make('razon_social')
                ->example('Empresa Ejemplo S.A.S')
                ->validationAttribute('Razón social inválida')
                ->rules(['nullable', 'max:255',]),
            ImportColumn::make('direccion')
                ->example('Calle 123 #45-67')
                ->validationAttribute('Dirección inválida')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('telefono')
                ->example('3001234567')
                ->validationAttribute('Teléfono inválido')
                ->rules(['nullable', 'max:255', 'unique:clientes,telefono']),
            ImportColumn::make('ciudad')
                ->example('Bogotá')
                ->validationAttribute('Ciudad inválida')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('email')
                ->example('cliente@email.com')
                ->validationAttribute('Email inválido')
                ->rules(['nullable', 'email', 'max:255', 'unique:clientes,email']),
            ImportColumn::make('representante_legal')
                ->example('Juan Pérez')
                ->validationAttribute('Representante legal inválido')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('activo')
                ->example('1')
                ->validationAttribute('Activo inválido')
                ->boolean()
                ->rules(['nullable', 'boolean']),
            ImportColumn::make('novedad')
                ->example('Nuevo')
                ->validationAttribute('Novedad inválida')
                ->rules(['max:255']),
            ImportColumn::make('ruta')
                ->example('Norte')
                ->relationship(resolveUsing: 'ruta')
                ->validationAttribute('Ruta Invalida')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('comercial') 
                ->example('Comercial')
                ->validationAttribute('Comercial inválido')
                ->relationship(resolveUsing: 'name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('tipo_cliente')
                ->example('MAYORISTA')
                ->validationAttribute('Tipo de cliente inválido')
                ->rules(['nullable', 'in:ELECTRONICO,REMISIONADO,MAYORISTA,MINORISTA,FERRETERO']),
            ImportColumn::make('rut_imagen')
                ->rules(['max:255']),
            ImportColumn::make('retenedor_fuente')
                ->example('SI')
                ->validationAttribute('Retenedor de fuente inválido')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): Cliente
    {
        return Cliente::firstOrNew([
            'numero_documento' => $this->data['numero_documento'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your cliente import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
