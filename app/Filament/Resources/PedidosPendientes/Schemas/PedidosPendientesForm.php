<?php

namespace App\Filament\Resources\PedidosPendientes\Schemas;

use App\Filament\Resources\Pedidos\Schemas\Concerns\HasPedidoSections;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
//use App\Filament\Resources\Pedidos\Schemas\Concerns\HasPedidoSections;

class PedidosPendientesForm
{
    use HasPedidoSections;

    public static function configure(Schema $schema): Schema
    {

        // Componer el schema usando las secciones del trait
        $components = array_merge(
            self::placeholders(),
            // solicitar la sección de datos generales en modo full para este formulario
            self::sectionDatosGenerales(true),
            //self::sectionResumen(),
            self::sectionComentarios(),
            self::sectionDetalles(),
            //self::sectionAbonos()
            self::sectionRecibido()
        );

        return $schema->components($components);
    }
}



