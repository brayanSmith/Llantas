<?php

namespace App\Filament\Resources\ComprasFacturadas;

use App\Filament\Resources\ComprasFacturadas\Pages\CreateComprasFacturadas;
use App\Filament\Resources\ComprasFacturadas\Pages\EditComprasFacturadas;
use App\Filament\Resources\ComprasFacturadas\Pages\ListComprasFacturadas;
use App\Filament\Resources\ComprasFacturadas\Schemas\ComprasFacturadasForm;
use App\Filament\Resources\ComprasFacturadas\Tables\ComprasFacturadasTable;
use App\Models\Compra;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ComprasFacturadasResource extends Resource
{
    protected static ?string $model = Compra::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Compra Facturada';
    protected static ?string $pluralModelLabel = 'Compras Facturadas';
    protected static ?string $navigationLabel = 'Facturadas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'titulo';
     protected static string|UnitEnum|null $navigationGroup = 'Compras';
     protected static ?string $navigationParentItem = 'Compras';
     protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:ComprasFacturadasResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:ComprasFacturadasResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:ComprasFacturadasResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:ComprasFacturadasResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:ComprasFacturadasResource');
    }

    public static function form(Schema $schema): Schema
    {
        return ComprasFacturadasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComprasFacturadasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Compras Facturadas';
    }
    public static function getPluralLabel(): string
    {
        return 'Compras Facturadas';
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::$model::where('estado', 'FACTURADO');
        /*if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }*/
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComprasFacturadas::route('/'),
            'create' => CreateComprasFacturadas::route('/create'),
            'edit' => EditComprasFacturadas::route('/{record}/edit'),
        ];
    }
}
