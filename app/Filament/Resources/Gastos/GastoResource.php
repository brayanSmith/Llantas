<?php

namespace App\Filament\Resources\Gastos;

use App\Filament\Resources\Gastos\Pages\ManageGastos;
use App\Models\Gasto;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;
    protected static ?string $modelLabel = 'Gastos';
    protected static ?string $pluralModelLabel = 'Gastos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $recordTitleAttribute = 'descripcion';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('descripcion')
                    ->required(),
                TextInput::make('monto')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('fecha_gasto')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                TextColumn::make('descripcion')
                    ->searchable(),
                TextColumn::make('monto')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fecha_gasto')
                    ->date()
                    ->sortable(),
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
            'index' => ManageGastos::route('/'),
        ];
    }
}
