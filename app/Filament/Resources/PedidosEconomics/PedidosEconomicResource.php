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

class PedidosEconomicResource extends Resource
{
    protected static ?string $model = Pedido::class;
    protected static ?string $modelLabel = 'Pedido Economic';
    protected static ?string $pluralModelLabel = 'Pedidos Economics';
    protected static ?string $navigationLabel = 'Pedidos Economic';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
}
