<?php

namespace App\Filament\Resources\Pedidos;

use App\Filament\Resources\Pedidos\Pages\CreatePedido;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Resources\Pedidos\Schemas\PedidoForm;
use App\Filament\Resources\Pedidos\Tables\PedidosTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    // Slug único para permisos de Shield
    protected static ?string $slug = 'pedidos';

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido General';
    protected static ?string $pluralModelLabel = 'Pedidos Generales';
    protected static ?string $navigationLabel = 'Pedidos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingCart;
    protected static string | UnitEnum | null $navigationGroup = 'Ventas';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidoResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidoResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:PedidoResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidoResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidoResource');
    }

    public static function form(Schema $schema): Schema
    {
        return PedidoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosTable::configure($table);
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
            'index' => ListPedidos::route('/'),
            //'create' => CreatePedido::route('/create'),
            'edit' => EditPedido::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderByDesc('created_at');
    }
}
