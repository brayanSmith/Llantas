<?php

namespace App\Filament\Resources\PedidosMayoristas\Pages;

use App\Filament\Resources\PedidosMayoristas\PedidosMayoristaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditPedidosMayorista extends EditRecord
{
    protected static string $resource = PedidosMayoristaResource::class;
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
            Action::make('download_pdf_facturado')
                ->label(fn () => 'Descargar PDF Facturado')
                //->icon('heroicon-o-document-download')
                ->url(fn () => route('pedidosFacturados.pdf.download', $this->record->id))
                ->openUrlInNewTab(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
