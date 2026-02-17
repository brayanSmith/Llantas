<?php

namespace App\Filament\Resources\AbonoCompras\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AbonoCompraInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('compra_id')
                    ->numeric(),
                TextEntry::make('fecha_abono_compra')
                    ->dateTime(),
                TextEntry::make('monto_abono_compra')
                    ->numeric(),
                TextEntry::make('forma_pago_abono_compra')
                    ->numeric(),
                TextEntry::make('descripcion_abono_compra')
                    ->placeholder('-'),
                TextEntry::make('imagen_abono_compra')
                    ->placeholder('-'),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
