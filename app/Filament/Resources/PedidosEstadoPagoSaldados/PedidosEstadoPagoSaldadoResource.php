<?php

namespace App\Filament\Resources\PedidosEstadoPagoSaldados;

use App\Filament\Resources\PedidosEstadoPagoSaldados\Pages\CreatePedidosEstadoPagoSaldado;
use App\Filament\Resources\PedidosEstadoPagoSaldados\Pages\EditPedidosEstadoPagoSaldado;
use App\Filament\Resources\PedidosEstadoPagoSaldados\Pages\ListPedidosEstadoPagoSaldados;
use App\Filament\Resources\PedidosEstadoPagoSaldados\Schemas\PedidosEstadoPagoSaldadoForm;
use App\Filament\Resources\PedidosEstadoPagoSaldados\Tables\PedidosEstadoPagoSaldadosTable;
use App\Models\Pedido;
use App\Policies\PedidoSaldadoPolicy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PedidosEstadoPagoSaldadoResource extends Resource
{
    protected static ?string $model = Pedido::class;
    
    // Slug único para permisos de Shield
    protected static ?string $slug = 'pedidos-saldados';
    
    // Política específica para este recurso
    protected static ?string $modelPolicy = PedidoSaldadoPolicy::class;
    
    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido Saldado';
    protected static ?string $pluralModelLabel = 'Pedidos Saldados';
    protected static ?string $navigationLabel = 'Saldados';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Cartera Ventas';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidosEstadoPagoSaldadoResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidosEstadoPagoSaldadoResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:PedidosEstadoPagoSaldadoResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidosEstadoPagoSaldadoResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidosEstadoPagoSaldadoResource');
    }

    public static function form(Schema $schema): Schema
    {
        return PedidosEstadoPagoSaldadoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosEstadoPagoSaldadosTable::configure($table);
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

    public static function getPages(): array
    {
        return [
            'index' => ListPedidosEstadoPagoSaldados::route('/'),
            //'create' => CreatePedidosEstadoPagoSaldado::route('/create'),
            'edit' => EditPedidosEstadoPagoSaldado::route('/{record}/edit'),
        ];
    }
}
