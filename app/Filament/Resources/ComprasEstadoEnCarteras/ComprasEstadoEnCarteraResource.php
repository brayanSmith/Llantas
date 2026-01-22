<?php

namespace App\Filament\Resources\ComprasEstadoEnCarteras;

use App\Filament\Resources\ComprasEstadoEnCarteras\Pages\CreateComprasEstadoEnCartera;
use App\Filament\Resources\ComprasEstadoEnCarteras\Pages\EditComprasEstadoEnCartera;
use App\Filament\Resources\ComprasEstadoEnCarteras\Pages\ListComprasEstadoEnCarteras;
use App\Filament\Resources\ComprasEstadoEnCarteras\Schemas\ComprasEstadoEnCarteraForm;
use App\Filament\Resources\ComprasEstadoEnCarteras\Tables\ComprasEstadoEnCarterasTable;
use App\Models\Compra;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ComprasEstadoEnCarteraResource extends Resource
{
    protected static ?string $model = Compra::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Compra En Cartera';
    protected static ?string $pluralModelLabel = 'Compras En Cartera';
    protected static ?string $navigationLabel = 'En Cartera';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'titulo';
     protected static string|UnitEnum|null $navigationGroup = 'Compras';
        //protected static ?string $navigationParentItem = 'Compras';
        //protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:ComprasEstadoEnCarteraResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:ComprasEstadoEnCarteraResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:ComprasEstadoEnCarteraResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:ComprasEstadoEnCarteraResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:ComprasEstadoEnCarteraResource');
    }

    public static function form(Schema $schema): Schema
    {
        return ComprasEstadoEnCarteraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComprasEstadoEnCarterasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Cuentas por Pagar';
    }
    public static function getPluralLabel(): string
    {
        return 'Cuentas por Pagar';
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::$model::where('estado_pago', 'EN_CARTERA')->whereIn('estado', ['FACTURADO']);
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComprasEstadoEnCarteras::route('/'),
            'create' => CreateComprasEstadoEnCartera::route('/create'),
            'edit' => EditComprasEstadoEnCartera::route('/{record}/edit'),
        ];
    }
}
