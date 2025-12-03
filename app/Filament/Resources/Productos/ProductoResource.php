<?php

namespace App\Filament\Resources\Productos;

use App\Filament\Resources\Productos\Pages\CreateProducto;
use App\Filament\Resources\Productos\Pages\EditProducto;
use App\Filament\Resources\Productos\Pages\ListProductos;
use App\Filament\Resources\Productos\Pages\ViewProducto;
use App\Filament\Resources\Productos\Schemas\ProductoForm;
use App\Filament\Resources\Productos\Schemas\ProductoInfolist;
use App\Filament\Resources\Productos\Tables\ProductosTable;
use App\Models\Producto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;


class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Gestión de Productos';
    protected static ?string $pluralModelLabel = 'Gestión de Productos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Productos';

    protected static ?string $recordTitleAttribute = 'codigo_producto';
    
    // Métodos de autorización independientes para Productos
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:ProductoResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:ProductoResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:ProductoResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:ProductoResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:ProductoResource');
    }

    public static function form(Schema $schema): Schema
    {
        return ProductoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductosTable::configure($table);
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
            'index' => ListProductos::route('/'),
            'create' => CreateProducto::route('/create'),
            'view' => ViewProducto::route('/{record}'),
            'edit' => EditProducto::route('/{record}/edit'),
        ];
    }
}
