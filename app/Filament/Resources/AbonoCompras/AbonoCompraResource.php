<?php

namespace App\Filament\Resources\AbonoCompras;

use App\Filament\Resources\AbonoCompras\Pages\CreateAbonoCompra;
use App\Filament\Resources\AbonoCompras\Pages\EditAbonoCompra;
use App\Filament\Resources\AbonoCompras\Pages\ListAbonoCompras;
use App\Filament\Resources\AbonoCompras\Pages\ViewAbonoCompra;
use App\Filament\Resources\AbonoCompras\Schemas\AbonoCompraForm;
use App\Filament\Resources\AbonoCompras\Schemas\AbonoCompraInfolist;
use App\Filament\Resources\AbonoCompras\Tables\AbonoComprasTable;
use App\Models\AbonoCompra;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AbonoCompraResource extends Resource
{
    protected static ?string $model = AbonoCompra::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pedido_id';

    public static function form(Schema $schema): Schema
    {
        return AbonoCompraForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AbonoCompraInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbonoComprasTable::configure($table);
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
            'index' => ListAbonoCompras::route('/'),
            'create' => CreateAbonoCompra::route('/create'),
            'view' => ViewAbonoCompra::route('/{record}'),
            'edit' => EditAbonoCompra::route('/{record}/edit'),
        ];
    }
}
