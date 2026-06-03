<?php

namespace App\Filament\Resources\PedidosEnCarteras;

use App\Filament\Resources\PedidosEnCarteras\Pages\CreatePedidosEnCartera;
use App\Filament\Resources\PedidosEnCarteras\Pages\EditPedidosEnCartera;
use App\Filament\Resources\PedidosEnCarteras\Pages\ListPedidosEnCarteras;
use App\Filament\Resources\PedidosEnCarteras\Schemas\PedidosEnCarteraForm;
use App\Filament\Resources\PedidosEnCarteras\Tables\PedidosEnCarterasTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PedidosEnCarteraResource extends Resource
{
    protected static ?string $model = Pedido::class;
    protected static ?string $modelLabel = 'Pedidos En Cartera';
    protected static ?string $pluralModelLabel = 'Pedidos En Carteras';
    protected static ?string $navigationLabel = 'Pedidos En Cartera';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Pedidos';
    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'Id';

    public static function form(Schema $schema): Schema
    {
        return PedidosEnCarteraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosEnCarterasTable::configure($table);
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
            'index' => ListPedidosEnCarteras::route('/'),
            'create' => CreatePedidosEnCartera::route('/create'),
            'edit' => EditPedidosEnCartera::route('/{record}/edit'),
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
                $q->whereIn('nombre_bodega', ['Economi', 'Outlet']);
            })
            ->where('estado_pago', 'EN_CARTERA')
            ->where('tipo_precio', 'DETAL');
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosEnCarteraResource');
    }
    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosEnCarteraResource');
    }
    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosEnCarteraResource');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosEnCarteraResource');
    }
}
