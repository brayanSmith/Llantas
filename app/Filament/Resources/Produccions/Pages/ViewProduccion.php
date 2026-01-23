<?php

namespace App\Filament\Resources\Produccions\Pages;

use App\Filament\Resources\Produccions\ProduccionResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduccion extends ViewRecord
{
    protected static string $resource = ProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('download_pdf')
                ->label(fn () => 'Descargar PDF')
                //->icon('heroicon-o-document-download')
                ->url(fn () => route('producciones.pdf.download', $this->record->id))
                ->openUrlInNewTab(),
        ];
    }
}
