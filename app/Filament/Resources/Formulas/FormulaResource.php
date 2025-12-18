<?php

namespace App\Filament\Resources\Formulas;

use App\Filament\Resources\Formulas\Pages\ManageFormulas;
use App\Models\Formula;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater\TableColumn;

use UnitEnum;

class FormulaResource extends Resource
{
    protected static ?string $model = Formula::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Producción';

    protected static ?string $recordTitleAttribute = 'nombre_formula';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre_formula')
                    ->required(),
                Textarea::make('descripcion_formula')
                    ->default(null)
                    ->columnSpanFull(),
                
                Repeater::make('detalleFormulas')
                    ->table([
                        TableColumn::make('MP')->width('80%'),
                        TableColumn::make('Cantidad')->width('20%'),                        
                    ])
                    ->relationship()
                    ->schema([
                        Select::make('producto_id')
                            ->label('Producto')
                            ->relationship(
                                name: 'producto',
                                titleAttribute: 'concatenar_codigo_nombre',
                                modifyQueryUsing: fn ($query) =>
                                    $query->where('categoria_producto', 'MATERIA_PRIMA')
                            )                            
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('cantidad_producto')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->required(),                        
                    ])
                    ->collapsible()                    
                    ->compact()
                    ->columns(2)
                    ->columnSpanFull()
                    ->defaultItems(1)
                    ->minItems(1)
                    ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre_formula')
            ->columns([
                TextColumn::make('nombre_formula')
                    ->searchable(),
                TextColumn::make('descripcion_formula')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ManageFormulas::route('/'),
        ];
    }
}
