<?php

namespace App\Filament\Resources\Comisions;

use App\Filament\Resources\Comisions\Pages\CreateComision;
use App\Filament\Resources\Comisions\Pages\EditComision;
use App\Filament\Resources\Comisions\Pages\ListComisions;
use App\Filament\Resources\Comisions\Pages\ViewComision;
use App\Filament\Resources\Comisions\Schemas\ComisionForm;
use App\Filament\Resources\Comisions\Schemas\ComisionInfolist;
use App\Filament\Resources\Comisions\Tables\ComisionsTable;
use App\Models\Comision;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ComisionResource extends Resource
{
    protected static ?string $model = Comision::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'periodo_inicial';

    public static function form(Schema $schema): Schema
    {
        return ComisionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ComisionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComisionsTable::configure($table);
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
            'index' => ListComisions::route('/'),
            'create' => CreateComision::route('/create'),
            'view' => ViewComision::route('/{record}'),
            'edit' => EditComision::route('/{record}/edit'),
        ];
    }
}
