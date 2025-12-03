<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Hash;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules([
                    'required', 
                    'email', 
                    'max:255',
                    'unique:users,email'
                ]),
            ImportColumn::make('email_verified_at')
                ->rules(['nullable', 'date']),
            ImportColumn::make('password')
                ->requiredMapping()
                ->rules(['required', 'string', 'min:8', 'max:255']),
        ];
    }

    public function resolveRecord(): User
    {
        // Encriptar la contraseña antes de guardar
        if (!empty($this->data['password'])) {
            $this->data['password'] = Hash::make($this->data['password']);
        }
        
        // Manejar email_verified_at
        if (empty($this->data['email_verified_at'])) {
            $this->data['email_verified_at'] = null;
        }
        
        // Remover role de los datos ya que no existe en la tabla
        unset($this->data['role']);
        
        // Usar solo new User() para evitar duplicados (ya validamos unique:users,email)
        return new User();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
