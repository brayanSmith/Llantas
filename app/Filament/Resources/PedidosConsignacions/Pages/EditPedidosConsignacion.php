<?php

namespace App\Filament\Resources\PedidosConsignacions\Pages;

use App\Filament\Resources\PedidosConsignacions\PedidosConsignacionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditPedidosConsignacion extends EditRecord
{
    protected static string $resource = PedidosConsignacionResource::class;
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
