<?php

namespace App\Filament\Resources\PedidosConsignacions;

use App\Filament\Resources\PedidosConsignacions\Pages\CreatePedidosConsignacion;
use App\Filament\Resources\PedidosConsignacions\Pages\EditPedidosConsignacion;
use App\Filament\Resources\PedidosConsignacions\Pages\ListPedidosConsignacions;
use App\Filament\Resources\PedidosConsignacions\Schemas\PedidosConsignacionForm;
use App\Filament\Resources\PedidosConsignacions\Tables\PedidosConsignacionsTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PedidosConsignacionResource extends Resource
{
    protected static ?string $model = Pedido::class;
    protected static ?string $modelLabel = 'Pedido Consignación';
    protected static ?string $pluralModelLabel = 'Pedidos Consignación';
    protected static ?string $navigationLabel = 'Pedidos Consignación';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Pedidos';
    //protected static ?string $navigationParentItem = 'Pedidos';
    protected static ?int $navigationSort = 5;
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return PedidosConsignacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosConsignacionsTable::configure($table);
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
            'index' => ListPedidosConsignacions::route('/'),
            'create' => CreatePedidosConsignacion::route('/create'),
            'edit' => EditPedidosConsignacion::route('/{record}/edit'),
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
        ->where('estado', 'PENDIENTE');
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
    // Permisos personalizados para Shield
    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosConsignacionResource');
    }
    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosConsignacionResource');
    }
    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosConsignacionResource');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosConsignacionResource');
    }
}

