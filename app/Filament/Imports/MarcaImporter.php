<?php

namespace App\Filament\Imports;

use App\Models\Marca;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class MarcaImporter extends Importer
{
    protected static ?string $model = Marca::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('marca')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('descripcion_marca'),
        ];
    }

    public function resolveRecord(): Marca
    {
        return Marca::firstOrNew([
            'marca' => $this->data['marca'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your marca import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
