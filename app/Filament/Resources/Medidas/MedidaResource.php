<?php

namespace App\Filament\Resources\Medidas;

use App\Filament\Resources\Medidas\Pages\ManageMedidas;
use App\Models\Medida;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use UnitEnum;

class MedidaResource extends Resource
{
    protected static ?string $model = Medida::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Producción';
    protected static ?string $recordTitleAttribute = 'nombre_medida';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre_medida')
                    ->required(),
                TextInput::make('descripcion_medida')
                    ->default(null),
                Radio::make('tipo_medida')
                    ->label('Tipo de Medida')
                    ->inline()
                    ->options([
                        'LONGITUD' => 'Longitud',
                        'PESO' => 'Peso',
                        'VOLUMEN' => 'Volumen',
                        'CANTIDAD' => 'Cantidad',
                    ])
                    ->required()
                    ->default('CANTIDAD'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre_medida')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('nombre_medida')
                    ->searchable(),
                TextColumn::make('descripcion_medida')
                    ->searchable(),
                TextColumn::make('tipo_medida')
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
            'index' => ManageMedidas::route('/'),
        ];
    }
}
