<?php

namespace App\Filament\Resources\PedidosOutlets;

use App\Filament\Resources\PedidosOutlets\Pages\CreatePedidosOutlet;
use App\Filament\Resources\PedidosOutlets\Pages\EditPedidosOutlet;
use App\Filament\Resources\PedidosOutlets\Pages\ListPedidosOutlets;
use App\Filament\Resources\PedidosOutlets\Schemas\PedidosOutletForm;
use App\Filament\Resources\PedidosOutlets\Tables\PedidosOutletsTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PedidosOutletResource extends Resource
{
    protected static ?string $model = Pedido::class;
    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido Outlet';
    protected static ?string $pluralModelLabel = 'Pedidos Outlets';
    protected static ?string $navigationLabel = 'Pedidos Outlet';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Ventas';
    //protected static ?string $navigationParentItem = 'Pedidos';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return PedidosOutletForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosOutletsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPedidosOutlets::route('/'),
            'create' => CreatePedidosOutlet::route('/create'),
            'edit' => EditPedidosOutlet::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::query()
            ->whereHas('bodega', function ($q) {
                $q->where('nombre_bodega', 'Outlet');
            })
            ->where('tipo_precio', 'DETAL')
            ->where('estado', 'COMPLETADO');
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    //Permisos de Spatie
    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosOutletResource');
    }
    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosOutletResource');
    }
    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosOutletResource');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosOutletResource');
    }
}
