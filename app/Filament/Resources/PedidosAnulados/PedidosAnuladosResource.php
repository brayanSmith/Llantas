<?php

namespace App\Filament\Resources\PedidosAnulados;

use App\Filament\Resources\PedidosAnulados\Pages\CreatePedidosAnulados;
use App\Filament\Resources\PedidosAnulados\Pages\EditPedidosAnulados;
use App\Filament\Resources\PedidosAnulados\Pages\ListPedidosAnulados;
use App\Filament\Resources\PedidosAnulados\Schemas\PedidosAnuladosForm;
use App\Filament\Resources\PedidosAnulados\Tables\PedidosAnuladosTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PedidosAnuladosResource extends Resource
{
    protected static ?string $model = Pedido::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido Anulado';
    protected static ?string $pluralModelLabel = 'Pedidos Anulados';
    protected static ?string $navigationLabel = 'Anulados';

    // Política específica para este recurso
    protected static ?string $modelPolicy = \App\Policies\PedidoAnuladoPolicy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Ventas';
    //protected static ?string $navigationParentItem = 'Pedidos';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosAnuladosResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosAnuladosResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:PedidosAnuladosResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosAnuladosResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosAnuladosResource');
    }

    public static function form(Schema $schema): Schema
    {
        return PedidosAnuladosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosAnuladosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Pedidos Anulados';
    }
    public static function getPluralLabel(): string
    {
        return 'Pedidos Anulados';
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::$model::where('estado', 'ANULADO');
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
    public static function getPages(): array
    {
        return [
            'index' => ListPedidosAnulados::route('/'),
            //'create' => CreatePedidosAnulados::route('/create'),
            'edit' => EditPedidosAnulados::route('/{record}/edit'),
        ];
    }
}
