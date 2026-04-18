<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class Cotizacion extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected string $view = 'filament.pages.cotizacion';
    protected static ?string $title = 'Cotizar';
    protected static ?string $slug = 'cotizar';

    public static function getNavigationGroup(): ?string
    {
        return 'Sistema';
    }

}
