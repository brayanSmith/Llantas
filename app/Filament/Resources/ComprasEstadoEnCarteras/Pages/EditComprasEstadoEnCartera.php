<?php

namespace App\Filament\Resources\ComprasEstadoEnCarteras\Pages;

use App\Filament\Resources\ComprasEstadoEnCarteras\ComprasEstadoEnCarteraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditComprasEstadoEnCartera extends EditRecord
{
    protected static string $resource = ComprasEstadoEnCarteraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //DeleteAction::make(),
            Action::make('download_pdf')
                ->label('Descargar PDF')
                ->url(fn () => route('compras-pdf.download', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
            Action::make('ver_pdf')
                ->label('Ver PDF')
                ->url(fn () => route('compras-pdf.stream', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
