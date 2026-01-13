<?php

namespace App\Filament\Resources\PedidosPendientes;

use App\Filament\Resources\PedidosPendientes\Pages\CreatePedidosPendientes;
use App\Filament\Resources\PedidosPendientes\Pages\EditPedidosPendientes;
use App\Filament\Resources\PedidosPendientes\Pages\ListPedidosPendientes;
use App\Filament\Resources\PedidosPendientes\Schemas\PedidosPendientesForm;
use App\Filament\Resources\PedidosPendientes\Tables\PedidosPendientesTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PedidosPendientesResource extends Resource
{
    protected static ?string $model = Pedido::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido Pendiente';
    protected static ?string $pluralModelLabel = 'Pedidos Pendientes';
    protected static ?string $navigationLabel = 'Pendientes';

    // Política específica para este recurso
    protected static ?string $modelPolicy = \App\Policies\PedidoPendientePolicy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;


    protected static string|UnitEnum|null $navigationGroup = 'Ventas';
    //protected static ?string $navigationParentItem = 'Pedidos';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosPendientesResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosPendientesResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:PedidosPendientesResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosPendientesResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosPendientesResource');
    }

    public static function form(Schema $schema): Schema
    {
        return PedidosPendientesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosPendientesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Pedidos Pendientes';
    }
    public static function getPluralLabel(): string
    {
        return 'Pedidos Pendientes';
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::$model::where('estado', 'PENDIENTE');
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
            'index' => ListPedidosPendientes::route('/'),
            //'create' => CreatePedidosPendientes::route('/create'),
            'edit' => EditPedidosPendientes::route('/{record}/edit'),
        ];
    }
}
