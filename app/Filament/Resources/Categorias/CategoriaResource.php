<?php

namespace App\Filament\Resources\Categorias;

use App\Filament\Resources\Categorias\Pages\CreateCategoria;
use App\Filament\Resources\Categorias\Pages\EditCategoria;
use App\Filament\Resources\Categorias\Pages\ListCategorias;
use App\Filament\Resources\Categorias\Schemas\CategoriaForm;
use App\Filament\Resources\Categorias\Tables\CategoriasTable;
use App\Models\Categoria;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;
    protected static ?string $modelLabel = 'Categorias';
    protected static ?string $pluralModelLabel = 'Categorias';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;
    protected static string|UnitEnum|null $navigationGroup = 'Productos';

    protected static ?string $recordTitleAttribute = 'nombre_categoria';

    public static function form(Schema $schema): Schema
    {
        return CategoriaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:CategoriaResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:CategoriaResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:CategoriaResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:CategoriaResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:CategoriaResource');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategorias::route('/'),
            'create' => CreateCategoria::route('/create'),
            'edit' => EditCategoria::route('/{record}/edit'),
        ];
    }
}
