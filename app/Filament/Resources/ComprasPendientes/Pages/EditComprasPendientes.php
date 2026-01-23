<?php

namespace App\Filament\Resources\ComprasPendientes\Pages;

use App\Filament\Resources\ComprasPendientes\ComprasPendientesResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComprasPendientes extends EditRecord
{
    protected static string $resource = ComprasPendientesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
