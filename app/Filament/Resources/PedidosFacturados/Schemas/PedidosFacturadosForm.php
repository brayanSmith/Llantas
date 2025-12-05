<?php

namespace App\Filament\Resources\PedidosFacturados\Schemas;
use App\Filament\Resources\Pedidos\Schemas\Concerns\HasPedidoSections;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;

use Filament\Schemas\Schema;

class PedidosFacturadosForm
{
    use HasPedidoSections;

    public static function configure(Schema $schema): Schema
    {
        // Construir pestañas: "General" (placeholders + datos + resumen + comentarios + detalles)
        // y "Recibido" (sección de recibido).  

        // Tomar la primera Section devuelta por cada helper (devuelven arrays de Section)
        $datosSections = self::sectionDatosGenerales();
        $resumenSections = self::sectionResumen();

        $datosSection = $datosSections[0] ?? null;
        $resumenSection = $resumenSections[0] ?? null;

        // Si ambas secciones existen, envolverlas en una Section padre de 2 columnas
        // para que se muestren una al lado de la otra. En caso contrario, usar
        // las secciones por separado dentro de la pestaña.
        if ($datosSection && $resumenSection) {
            $datosYResumen = Section::make()
                ->columns(2)
                ->schema([ 
                    // Cada Section interna ocupará una columna
                    $datosSection->columnSpan(1),
                    $resumenSection->columnSpan(1),
                ])
                ->columnSpanFull();

            $generalContents = array_merge(
                self::placeholders(),
                [$datosYResumen],
                self::sectionComentarios(),
                self::sectionDetalles(),
            );
        } else {
            // Alternativa: incluir las secciones tal como las devuelve el trait
            $generalContents = array_merge(
                self::placeholders(),
                self::sectionDatosGenerales(),
                self::sectionResumen(),
                self::sectionComentarios(),
                self::sectionDetalles(),
            );
        }

        $tab1 = $generalContents;
        $tab2 = self::sectionRecibido();

        $tabs = Tabs::make('PedidoTabs')
            ->tabs([
                Tab::make('General')
                    ->schema($tab1),
                Tab::make('Recibido')
                    ->schema($tab2),
            ])
            //->horizontal()
            ->columnSpanFull();

        return $schema->components([
            $tabs,
        ]);
    }
}
