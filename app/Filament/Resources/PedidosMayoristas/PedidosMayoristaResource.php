<?php

namespace App\Filament\Resources\PedidosMayoristas;

use App\Filament\Resources\PedidosMayoristas\Pages\CreatePedidosMayorista;
use App\Filament\Resources\PedidosMayoristas\Pages\EditPedidosMayorista;
use App\Filament\Resources\PedidosMayoristas\Pages\ListPedidosMayoristas;
use App\Filament\Resources\PedidosMayoristas\Schemas\PedidosMayoristaForm;
use App\Filament\Resources\PedidosMayoristas\Tables\PedidosMayoristasTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PedidosMayoristaResource extends Resource
{
    protected static ?string $model = Pedido::class;
    protected static ?string $modelLabel = 'Pedido Mayorista';
    protected static ?string $pluralModelLabel = 'Pedidos Mayoristas';
    protected static ?string $navigationLabel = 'Pedidos Mayorista';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return PedidosMayoristaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosMayoristasTable::configure($table);
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
            'index' => ListPedidosMayoristas::route('/'),
            'create' => CreatePedidosMayorista::route('/create'),
            'edit' => EditPedidosMayorista::route('/{record}/edit'),
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
