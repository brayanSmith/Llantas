<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class AlpinePrecioCantidad extends TextInput
{
    public static function alpineBindings($get): array
    {
        $cantidad = $get('cantidad') ?? 1;
        $precio = $get('precio_unitario') ?? 0;
        $conIva = $get('aplicar_iva');
        $conIva = is_null($conIva) ? true : (bool)$conIva;
        $ivaFactor = $get('iva') ?? 0.19;
        return [
            'x-data' => "{ cantidad: $cantidad, precio: $precio, conIva: " . ($conIva ? 'true' : 'false') . ", ivaFactor: $ivaFactor }",
        ];
    }
}
