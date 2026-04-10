<?php

namespace App\Filament\Resources\PedidosEconomics;

use App\Filament\Resources\PedidosEconomics\Pages\CreatePedidosEconomic;
use App\Filament\Resources\PedidosEconomics\Pages\EditPedidosEconomic;
use App\Filament\Resources\PedidosEconomics\Pages\ListPedidosEconomics;
use App\Filament\Resources\PedidosEconomics\Schemas\PedidosEconomicForm;
use App\Filament\Resources\PedidosEconomics\Tables\PedidosEconomicsTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PedidosEconomicResource extends Resource
{
    protected static ?string $model = Pedido::class;
    protected static ?string $modelLabel = 'Pedido Economic';
    protected static ?string $pluralModelLabel = 'Pedidos Economics';
    protected static ?string $navigationLabel = 'Pedidos Economic';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Ventas';
    //protected static ?string $navigationParentItem = 'Pedidos';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return PedidosEconomicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosEconomicsTable::configure($table);
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
            'index' => ListPedidosEconomics::route('/'),
            'create' => CreatePedidosEconomic::route('/create'),
            'edit' => EditPedidosEconomic::route('/{record}/edit'),
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
                $q->where('nombre_bodega', 'Economi');
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
    //permisos para shield
    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosEconomicResource');
    }
    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosEconomicResource');
    }
    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosEconomicResource');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosEconomicResource');
    }
}
