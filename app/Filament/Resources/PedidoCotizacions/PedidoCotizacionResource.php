<?php

namespace App\Filament\Resources\PedidoCotizacions;

use App\Filament\Resources\PedidoCotizacions\Pages\CreatePedidoCotizacion;
use App\Filament\Resources\PedidoCotizacions\Pages\EditPedidoCotizacion;
use App\Filament\Resources\PedidoCotizacions\Pages\ListPedidoCotizacions;
use App\Filament\Resources\PedidoCotizacions\Schemas\PedidoCotizacionForm;
use App\Filament\Resources\PedidoCotizacions\Tables\PedidoCotizacionsTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PedidoCotizacionResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Ventas';
     protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'codigo';

    protected static ?string $modelLabel = 'Pedido Cotización';
    protected static ?string $pluralModelLabel = 'Pedidos Cotizaciones';
    protected static ?string $navigationLabel = 'Cotizaciones';


    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('super_admin') || auth()->user()->can('ViewAny:PedidoCotizacionResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->hasRole('super_admin') || auth()->user()->can('View:PedidoCotizacionResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('super_admin') || auth()->user()->can('Create:PedidoCotizacionResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('super_admin') || auth()->user()->can('Update:PedidoCotizacionResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('super_admin') || auth()->user()->can('Delete:PedidoCotizacionResource');
    }

    public static function form(Schema $schema): Schema
    {
        return PedidoCotizacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidoCotizacionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Cotizaciones';
    }

    public static function getPluralLabel(): string
    {
        return 'Cotizaciones';
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::$model::where('estado', 'PENDIENTE')->where('estado_venta', 'COTIZACION');
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }


    public static function getPages(): array
    {
        return [
            'index' => ListPedidoCotizacions::route('/'),
            'create' => CreatePedidoCotizacion::route('/create'),
            'edit' => EditPedidoCotizacion::route('/{record}/edit'),
        ];
    }
}
