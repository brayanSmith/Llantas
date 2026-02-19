<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AbonoPedido extends Page
{
    protected string $view = 'filament.pages.abono-pedido';

    protected static ?string $title = 'Registrar Abono a Pedido';

    protected static bool $shouldRegisterNavigation = false;
}
