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

class PedidosConsignacionResource extends Resource
{
    protected static ?string $model = Pedido::class;
    protected static ?string $modelLabel = 'Pedido Consignación';
    protected static ?string $pluralModelLabel = 'Pedidos Consignación';
    protected static ?string $navigationLabel = 'Pedidos Consignación';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
}
