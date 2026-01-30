<?php

namespace App\Filament\Resources\PedidoEnRutas;

use BackedEnum;
use App\Models\Pedido;
use Filament\Tables\Table;
use App\Models\PedidoEnRuta;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;

use App\Policies\PedidoEnRutaPolicy;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\PedidoEnRutas\Pages\EditPedidoEnRuta;
use App\Filament\Resources\PedidoEnRutas\Pages\ListPedidoEnRutas;
use App\Filament\Resources\PedidoEnRutas\Pages\CreatePedidoEnRuta;
use App\Filament\Resources\PedidoEnRutas\Schemas\PedidoEnRutaForm;
use App\Filament\Resources\PedidoEnRutas\Tables\PedidoEnRutasTable;
use UnitEnum;

class PedidoEnRutaResource extends Resource
{
    protected static ?string $model = Pedido::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido En Ruta';
    protected static ?string $pluralModelLabel = 'Pedidos En Ruta';
    protected static ?string $navigationLabel = 'Pedidos En Ruta';

        // Política específica para este recurso
    protected static ?string $modelPolicy = PedidoEnRutaPolicy::class;


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Ventas';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidoEnRutaResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidoEnRutaResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:PedidoEnRutaResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidoEnRutaResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidoEnRutaResource');
    }

    public static function form(Schema $schema): Schema
    {
        return PedidoEnRutaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidoEnRutasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Pedidos En Ruta';
    }

    public static function getPluralLabel(): string
    {
        return 'Pedidos En Ruta';
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::$model::whereIn('estado', ['FACTURADO', 'EN_RUTA']);
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPedidoEnRutas::route('/'),
            'create' => CreatePedidoEnRuta::route('/create'),
            'edit' => EditPedidoEnRuta::route('/{record}/edit'),
        ];
    }
}
