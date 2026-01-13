<?php

namespace App\Filament\Resources\PedidosEstadoPagoEnCarteras;


use App\Filament\Resources\PedidosEstadoPagoEnCarteras\Pages\CreatePedidosEstadoPagoEnCartera;
use App\Filament\Resources\PedidosEstadoPagoEnCarteras\Pages\EditPedidosEstadoPagoEnCartera;
use App\Filament\Resources\PedidosEstadoPagoEnCarteras\Pages\ListPedidosEstadoPagoEnCarteras;
use App\Filament\Resources\PedidosEstadoPagoEnCarteras\Schemas\PedidosEstadoPagoEnCarteraForm;
use App\Filament\Resources\PedidosEstadoPagoEnCarteras\Tables\PedidosEstadoPagoEnCarterasTable;
use App\Models\Pedido;
use App\Policies\PedidoCarteraPolicy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PedidosEstadoPagoEnCarteraResource extends Resource
{
    protected static ?string $model = Pedido::class;

    // Slug único para permisos de Shield
    protected static ?string $slug = 'pedidos-en-cartera';

    // Política específica para este recurso
    protected static ?string $modelPolicy = PedidoCarteraPolicy::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido En Cartera';
    protected static ?string $pluralModelLabel = 'Pedidos En Cartera';
    protected static ?string $navigationLabel = 'En Cartera';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Cartera Ventas';
    //protected static ?string $navigationParentItem = 'Pedidos';
    //protected static ?int $navigationSort = 1;


    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosEstadoPagoEnCarteraResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosEstadoPagoEnCarteraResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:PedidosEstadoPagoEnCarteraResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosEstadoPagoEnCarteraResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosEstadoPagoEnCarteraResource');
    }

    public static function form(Schema $schema): Schema
    {
        return PedidosEstadoPagoEnCarteraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosEstadoPagoEnCarterasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationLabel(): string
    {
        return 'Cuentas por Cobrar';
    }
    public static function getPluralLabel(): string
    {
        return 'Cuentas por Cobrar';
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::$model::where('estado_pago', 'EN_CARTERA')->whereIn('estado', ['FACTURADO', 'EN_RUTA', 'ENTREGADO']);
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
            'index' => ListPedidosEstadoPagoEnCarteras::route('/'),
            //'create' => CreatePedidosEstadoPagoEnCartera::route('/create'),
            'edit' => EditPedidosEstadoPagoEnCartera::route('/{record}/edit'),
        ];
    }
}
