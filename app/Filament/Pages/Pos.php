<?php

namespace App\Filament\Pages;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

use Filament\Pages\Page;

class Pos extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    protected static ?string $title = 'POS - Punto de Venta';
    protected static ?string $navigationLabel = 'POS';
    protected static ?string $slug = 'pos';

    public static function getNavigationLabel(): string
    {
        return 'POS';
    }
    
    public static function canAccess(): bool
    {
        return auth()->check() && (
            auth()->user()->can('View:Pos') || 
            auth()->user()->can('view_pos') ||
            auth()->user()->can('view_App\\Filament\\Pages\\Pos')
        );
    }

    // Método para Shield - define el nombre que aparece en la gestión de permisos
    public static function getModelLabel(): string
    {
        return 'POS - Punto de Venta';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'POS - Punto de Venta';
    }
    
    // Método que Shield usa para obtener el nombre de la página
    public static function getLabel(): string
    {
        return 'POS - Punto de Venta';
    }

    //vamos hacer que el titulo no se muestre en la página
    public function getTitle(): string
    {
        return '';
    }
    /*public static function getPluralLabel(): string
    {
        return 'Pedidos Anulados';
    }*/

    protected string $view = 'filament.pages.pos';
}
