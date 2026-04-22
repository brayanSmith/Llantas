<?php

namespace App\Filament\Resources\Catalogos;

use App\Filament\Resources\Catalogos\Pages\ManageCatalogos;
use App\Models\Producto;
use BackedEnum;
use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CatalogoResource extends Resource
{
    protected static ?string $model = Producto::class;

    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Catálogo de Productos';
    protected static ?string $pluralModelLabel = 'Catálogo de Productos';
    protected static ?string $navigationLabel = 'Catalogos';

    // Métodos de autorización independientes para Catálogo
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:CatalogoResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:CatalogoResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:CatalogoResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:CatalogoResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:CatalogoResource');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;
    protected static string|UnitEnum|null $navigationGroup = 'Productos';

    protected static ?string $recordTitleAttribute = 'codigo_producto';

    public static function getNavigationLabel(): string
    {
        return 'Catálogo';
    }

    //vamos hacer que el titulo no se muestre
    /*public function getTitle(): string
    {
        return '';
    }*/
    public static function getPluralLabel(): string
    {
        return 'Catálogo';
    }

    /*public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo_producto')
                    ->required()
                    ->maxLength(255),
            ]);
    }*/

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('codigo_producto'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo_producto')
            ->modifyQueryUsing(fn ($query) => $query->withSum('stockBodegas', 'stock'))
            ->columns([
                Stack::make([
                    TextColumn::make('concatenar_codigo_nombre')
                        ->alignCenter()
                        ->searchable()
                        ->weight('bold'),
                    ImageColumn::make('imagen_producto')
                        ->label('Imagen')
                        ->disk('public')
                        //->circular()
                        ->height(100)
                        ->width(100)
                        ->alignCenter(),
                    TextColumn::make('codigo_producto')
                        ->alignCenter()
                        ->searchable()
                        ->extraAttributes([
                            'class' => 'text-center text-xs text-yellow-500 font-bold', // pequeño, amarillo y negrita
                        ]),
                    TextColumn::make('referencia_producto')
                        ->alignCenter()
                        ->searchable()
                        ->extraAttributes([
                            'class' => 'text-center text-xs text-blue-500 font-bold', // pequeño, azul y negrita
                        ]),
                    TextColumn::make('descripcion_producto')
                        ->alignCenter()
                        ->searchable()
                        ->extraAttributes([
                            'class' => 'text-center text-xs text-gray-500', // pequeño y gris
                        ]),
                    TextColumn::make('valor_mayorista')
                        ->alignCenter()
                        ->label('Valor x Mayor')
                        ->formatStateUsing(fn($state) => 'Valor x Mayor: $' . number_format($state, 0)),

                    TextColumn::make('stock_bodegas_sum_stock')
                        ->alignCenter()
                        ->label('Stock')
                        ->badge()
                        ->color(fn($state) => ((int) ($state ?? 0)) > 0 ? 'success' : 'danger')
                        ->formatStateUsing(fn($state) => 'Stock: ' . number_format((int) ($state ?? 0), 0)),

                ])
            ])
            ->contentGrid([
                'xs' => 2,
                'sm' => 2,
                'md' => 4,
                'lg' => 4,
                'xl' => 4,
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //ViewAction::make(),
                //EditAction::make(),
                //DeleteAction::make(),
            ])
            ->toolbarActions([
                /*BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),*/]);
    }


    public static function getPages(): array
    {
        return [
            'index' => ManageCatalogos::route('/'),
        ];
    }
}
