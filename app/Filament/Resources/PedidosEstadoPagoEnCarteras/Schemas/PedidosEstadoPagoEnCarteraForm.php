<?php

namespace App\Filament\Resources\PedidosEstadoPagoEnCarteras\Schemas;
use App\Filament\Resources\Pedidos\Schemas\Concerns\HasPedidoSections;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PedidosEstadoPagoEnCarteraForm
{
    use HasPedidoSections;

    public static function configure(Schema $schema): Schema
    {
        // Construir pestañas: "General" (placeholders + datos + resumen + comentarios + detalles)
        // y "Recibido" (sección de recibido).

        // Tomar la primera Section devuelta por cada helper (devuelven arrays de Section)
        $datosSections = self::sectionDatosGenerales(false, ['FACTURADO' => 'Facturado', 'EN_RUTA' => 'En Ruta', 'ENTREGADO' => 'Entregado']); 
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
                
            );
        } else {
            // Alternativa: incluir las secciones tal como las devuelve el trait
            $generalContents = array_merge(
                self::placeholders(),
                self::sectionDatosGenerales(),
                self::sectionResumen(),
                self::sectionComentarios(),                
            );
        }

        $tab1 = $generalContents;
        $tab2 = self::sectionDetalles();
        $tab3 = self::sectionAbonos();
        $tab4 = self::sectionRecibido();

        $tabs = Tabs::make('PedidoTabs')
            ->tabs([
                Tab::make('General')
                    ->schema($tab1),
                Tab::make('Detalles')
                    ->schema($tab2),
                Tab::make('Abonos')
                    ->schema($tab3),
                Tab::make('Recibido')
                    ->schema($tab4),
            ])
            //->horizontal()
            ->columnSpanFull();

        return $schema->components([
            $tabs,
        ]);
    }
}
