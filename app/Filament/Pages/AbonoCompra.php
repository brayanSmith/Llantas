<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AbonoCompra extends Page
{
    protected string $view = 'filament.pages.abono-compra';

    protected static ?string $title = 'Registrar Abono a Compra';

    protected static bool $shouldRegisterNavigation = false;
}
