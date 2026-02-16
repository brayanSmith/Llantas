<?php

namespace App\Filament\Resources\ComprasFacturadas\Pages;

use App\Filament\Resources\ComprasFacturadas\ComprasFacturadasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditComprasFacturadas extends EditRecord
{
    protected static string $resource = ComprasFacturadasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getFormActions(): array
    {
        return [];
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
