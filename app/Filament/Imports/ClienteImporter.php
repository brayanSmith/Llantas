<?php

namespace App\Filament\Imports;

use App\Models\Cliente;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ClienteImporter extends Importer
{
    protected static ?string $model = Cliente::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('tipo_documento')
                ->example('CC')                
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('numero_documento')
                    ->example('1234567890')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('razon_social')
                ->example('Empresa Ejemplo S.A.S')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('direccion')
                ->example('Calle 123 #45-67')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('telefono')
                ->example('3001234567')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('ciudad')
                ->example('Bogotá')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->example('example@example.com')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('representante_legal')
                ->example('Juan Pérez')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('activo')
                ->example('1')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('novedad')
                ->example('Nuevo')
                ->rules(['max:255']),
            ImportColumn::make('ruta')
                ->example('Ruta 1')
                ->relationship(resolveUsing: 'nombre_ruta')
                ->rules(['max:255']),
            ImportColumn::make('comercial')                
                ->example('Juan Comercial')
                ->relationship(resolveUsing: ['name', 'email']) 
                ->rules(['max:255']),
            ImportColumn::make('tipo_cliente')
                ->example('MAYORISTA')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('rut_imagen')
                ->rules(['max:255']),
            ImportColumn::make('retenedor_fuente')
                ->example('SI')
                ->requiredMapping()
                ->rules(['required']),
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
