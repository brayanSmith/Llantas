<?php

namespace App\Filament\Resources\Marcas;

use App\Filament\Resources\Marcas\Pages\ManageMarcas;
use App\Models\Marca;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MarcaResource extends Resource
{
    protected static ?string $model = Marca::class;
    protected static ?string $modelLabel = 'Marcas';
    protected static ?string $pluralModelLabel = 'Marcas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFingerPrint;
    protected static string|UnitEnum|null $navigationGroup = 'Productos';

    protected static ?string $recordTitleAttribute = 'marca';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:MarcaResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:MarcaResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:MarcaResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:MarcaResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:MarcaResource');
    }


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('marca')
                    ->required(),
                TextInput::make('descripcion_marca'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('marca')
            ->columns([
                TextColumn::make('marca')
                    ->searchable(),
                TextColumn::make('descripcion_marca')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMarcas::route('/'),
        ];
    }
}
