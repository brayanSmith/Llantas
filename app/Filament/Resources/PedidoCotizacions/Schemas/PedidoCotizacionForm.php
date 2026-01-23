<?php

namespace App\Filament\Resources\PedidoCotizacions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\PedidoCotizacions\Schemas\PedidoCotizacionDetail;
use App\Filament\Resources\PedidoCotizacions\Schemas\PedidoCotizacionDatosGenerales;


class PedidoCotizacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...PedidoCotizacionDatosGenerales::sectionDatosGenerales(true),
                ...PedidoCotizacionDetail::sectionDetalles(),
            ]);
    }
}
