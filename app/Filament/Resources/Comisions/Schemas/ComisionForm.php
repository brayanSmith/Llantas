<?php

namespace App\Filament\Resources\Comisions\Schemas;

use App\Filament\Forms\Components\ComisionFormTable;
use Filament\Schemas\Schema;

class ComisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComisionFormTable::make('comision_data')
                    ->columnSpanFull()
                    ->hiddenLabel(),
            ]);
    }
}

