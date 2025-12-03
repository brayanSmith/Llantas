<?php

namespace App\Filament\Resources\PedidosFacturados;

use App\Filament\Resources\PedidosFacturados\Pages\CreatePedidosFacturados;
use App\Filament\Resources\PedidosFacturados\Pages\EditPedidosFacturados;
use App\Filament\Resources\PedidosFacturados\Pages\ListPedidosFacturados;
use App\Filament\Resources\PedidosFacturados\Schemas\PedidosFacturadosForm;
use App\Filament\Resources\PedidosFacturados\Tables\PedidosFacturadosTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Table;
use UnitEnum;


class PedidosFacturadosResource extends Resource
{
    protected static ?string $model = Pedido::class;
    
    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido Facturado';
    protected static ?string $pluralModelLabel = 'Pedidos Facturados';
    protected static ?string $navigationLabel = 'Facturados';
    
    // Política específica para este recurso
    protected static ?string $modelPolicy = \App\Policies\PedidoFacturadoPolicy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Ventas';
    //protected static ?string $navigationParentItem = 'Pedidos';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosFacturadosResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosFacturadosResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:PedidosFacturadosResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosFacturadosResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosFacturadosResource');
    }

    public static function form(Schema $schema): Schema
    {
        return PedidosFacturadosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosFacturadosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationLabel(): string
    {
        return 'Pedidos Facturados';
    }

    public static function getPluralLabel(): string
    {
        return 'Pedidos Facturados';
    }
    public static function getPages(): array
    {
        return [
            'index' => ListPedidosFacturados::route('/'),
            //'create' => CreatePedidosFacturados::route('/create'),
            'edit' => EditPedidosFacturados::route('/{record}/edit'),
        ];
    }
}
