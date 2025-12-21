<?php

namespace App\Filament\Resources\Comisions\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Comision;

class ComisionFormEstado
{
    public static function getFormFields(): array
    {
        return [
            ToggleButtons::make('estado_comision')
                ->label('Selecciona el nuevo estado')
                ->options([
                    'PENDIENTE' => 'Pendiente',
                    'PAGADA' => 'Pagada',
                    'RECHAZADA' => 'Rechazada',
                ])
                ->colors([
                    'PENDIENTE' => 'warning',
                    'PAGADA' => 'success',
                    'RECHAZADA' => 'danger',
                ])
                ->icons([
                    'PENDIENTE' => 'heroicon-o-clock',
                    'PAGADA' => 'heroicon-o-check-circle',
                    'RECHAZADA' => 'heroicon-o-x-circle',
                ])
                ->inline()
                ->required(),
        ];
    }

    public static function getAction(): Action
    {
        return Action::make('cambiar_estado')
            ->label('Cambiar Estado')
            ->icon('heroicon-o-arrow-path')
            ->color('primary')
            ->modalHeading('Cambiar Estado de la Comisión')
            ->modalWidth('md')
            ->form(static::getFormFields())
            ->fillForm(fn (Comision $record) => ['estado_comision' => $record->estado_comision])
            ->modalSubmitActionLabel('Actualizar Estado')
            ->action(function (Comision $record, array $data) {
                $record->estado_comision = $data['estado_comision'];
                $record->save();
                
                Notification::make()
                    ->title('Estado actualizado')
                    ->body('La comisión ahora está marcada como: ' . $data['estado_comision'])
                    ->success()
                    ->send();
            });
    }

    public static function getBulkAction(): BulkAction
    {
        return BulkAction::make('cambiar_estado_bulk')
            ->label('Cambiar Estado')
            ->icon('heroicon-o-arrow-path')
            ->color('primary')
            ->modalHeading('Cambiar Estado de las Comisiones Seleccionadas')
            ->modalWidth('md')
            ->form(static::getFormFields())
            ->modalSubmitActionLabel('Actualizar Estado')
            ->action(function (Collection $records, array $data) {
                $records->each(function (Comision $record) use ($data) {
                    $record->estado_comision = $data['estado_comision'];
                    $record->save();
                });
                
                Notification::make()
                    ->title('Estados actualizados')
                    ->body($records->count() . ' comisiones actualizadas a: ' . $data['estado_comision'])
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}