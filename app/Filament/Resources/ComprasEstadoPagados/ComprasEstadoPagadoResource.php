<?php

namespace App\Filament\Resources\ComprasEstadoPagados;

use App\Filament\Resources\ComprasEstadoPagados\Pages\CreateComprasEstadoPagado;
use App\Filament\Resources\ComprasEstadoPagados\Pages\EditComprasEstadoPagado;
use App\Filament\Resources\ComprasEstadoPagados\Pages\ListComprasEstadoPagados;
use App\Filament\Resources\ComprasEstadoPagados\Schemas\ComprasEstadoPagadoForm;
use App\Filament\Resources\ComprasEstadoPagados\Tables\ComprasEstadoPagadosTable;
use App\Models\Compra;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ComprasEstadoPagadoResource extends Resource
{
    protected static ?string $model = Compra::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Compra Pagada';
    protected static ?string $pluralModelLabel = 'Compras Pagadas';
    protected static ?string $navigationLabel = 'Pagadas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'titulo';
     protected static string|UnitEnum|null $navigationGroup = 'Compras';
        //protected static ?string $navigationParentItem = 'Compras';
        //protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:ComprasEstadoPagadoResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:ComprasEstadoPagadoResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:ComprasEstadoPagadoResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:ComprasEstadoPagadoResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:ComprasEstadoPagadoResource');
    }

    public static function form(Schema $schema): Schema
    {
        return ComprasEstadoPagadoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComprasEstadoPagadosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Cuentas Pagadas';
    }
    public static function getPluralLabel(): string
    {
        return 'Cuentas Pagadas';
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::$model::where('estado_pago', 'SALDADO')->where('estado', 'FACTURADO');
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
            'index' => ListComprasEstadoPagados::route('/'),
            'create' => CreateComprasEstadoPagado::route('/create'),
            'edit' => EditComprasEstadoPagado::route('/{record}/edit'),
        ];
    }
}
