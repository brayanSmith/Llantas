<?php

namespace App\Filament\Resources\Compras\Pages;

use App\Filament\Resources\Compras\CompraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditCompra extends EditRecord
{
    protected static string $resource = CompraResource::class;

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
