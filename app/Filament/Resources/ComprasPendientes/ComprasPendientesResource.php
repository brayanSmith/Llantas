<?php

namespace App\Filament\Resources\ComprasPendientes;

use App\Filament\Resources\ComprasPendientes\Pages\CreateComprasPendientes;
use App\Filament\Resources\ComprasPendientes\Pages\EditComprasPendientes;
use App\Filament\Resources\ComprasPendientes\Pages\ListComprasPendientes;
use App\Filament\Resources\ComprasPendientes\Schemas\ComprasPendientesForm;
use App\Filament\Resources\ComprasPendientes\Tables\ComprasPendientesTable;
use App\Models\Compra;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ComprasPendientesResource extends Resource
{
    protected static ?string $model = Compra::class;
    protected static ?string $modelLabel = 'Compra Pendiente';
    protected static ?string $pluralModelLabel = 'Compras Pendientes';
    protected static ?string $navigationLabel = 'Compras Pendientes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPathRoundedSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Compras';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return ComprasPendientesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComprasPendientesTable::configure($table);
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
            'index' => ListComprasPendientes::route('/'),
            'create' => CreateComprasPendientes::route('/create'),
            'edit' => EditComprasPendientes::route('/{record}/edit'),
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
            ->where('estado', 'PENDIENTE');
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:ComprasPendientesResource');
    }
    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:ComprasPendientesResource');
    }
    public static function canView($record): bool
    {
        return auth()->user()->can('View:ComprasPendientesResource');
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:ComprasPendientesResource');
    }
}
