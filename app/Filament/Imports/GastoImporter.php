<?php

namespace App\Filament\Imports;

use App\Models\Gasto;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class GastoImporter extends Importer
{
    protected static ?string $model = Gasto::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('cuenta_gasto')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('subcuenta_gasto')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('concepto_gasto')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('descripcion_gasto')
                ->rules(['max:255']),
            ImportColumn::make('concatenar_subcuenta_concepto')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): Gasto
    {
        return Gasto::firstOrNew([
            'subcuenta_gasto' => $this->data['subcuenta_gasto'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your gasto import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
