<?php

namespace App\Filament\Resources\Traslados;

use App\Filament\Resources\Traslados\Pages\ManageTraslados;
use App\Models\Traslado;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use App\Models\Producto;
use App\Models\StockBodega;
use App\Services\StockCalculoService;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use UnitEnum;

class TrasladoResource extends Resource
{
    protected static ?string $model = Traslado::class;
    protected static ?string $modelLabel = 'Traslados';
    protected static ?string $pluralModelLabel = 'Traslados';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;
    protected static string|UnitEnum|null $navigationGroup = 'Stock';

    protected static ?string $recordTitleAttribute = 'producto_id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bodega_donante_id')
                    ->relationship('bodegaDonante', 'nombre_bodega')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('producto_id', null))
                    ->searchable(),

                Select::make('bodega_destino_id')
                    ->relationship('bodegaDestino', 'nombre_bodega')
                    ->required(),
                Select::make('producto_id')
                    ->label('Producto')
                    ->options(function ($get) {
                        $bodegaDonanteId = $get('bodega_donante_id');

                        if (!$bodegaDonanteId) {
                            return [];
                        }

                        return Producto::whereHas('stockBodegas', function ($query) use ($bodegaDonanteId) {
                            $query->where('bodega_id', $bodegaDonanteId)
                                  ->where('entradas', '>', 0);
                        })
                        ->with(['stockBodegas' => function ($query) use ($bodegaDonanteId) {
                            $query->where('bodega_id', $bodegaDonanteId);
                        }])
                        ->get()
                        ->mapWithKeys(function ($producto) {
                            $entradas = $producto->stockBodegas->first()->entradas ?? 0;
                            return [$producto->id => "{$producto->concatenar_codigo_nombre} (Stock: {$entradas})"];
                        });
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('cantidad', null)),
                TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->placeholder(function ($get) {
                        $productoId = $get('producto_id');
                        $bodegaDonanteId = $get('bodega_donante_id');

                        if (!$productoId || !$bodegaDonanteId) {
                            return 'Seleccione un producto';
                        }

                        $stockCalculoService = app(StockCalculoService::class);
                        $stockDisponible = $stockCalculoService->calcularEntradasFacturadas($productoId, $bodegaDonanteId);

                        return "Disponible: {$stockDisponible}";
                    })
                    ->rules([
                        function ($get) {
                            return function (string $attribute, $value, $fail) use ($get) {
                                $productoId = $get('producto_id');
                                $bodegaDonanteId = $get('bodega_donante_id');

                                if (!$productoId || !$bodegaDonanteId) {
                                    return;
                                }

                                $stockCalculoService = app(StockCalculoService::class);
                                $stockDisponible = $stockCalculoService->calcularEntradasFacturadas($productoId, $bodegaDonanteId);

                                if ($value > $stockDisponible) {
                                    $fail("La cantidad no puede ser mayor a {$stockDisponible} (stock disponible).");
                                }
                            };
                        },
                    ]),
                TextArea::make('observaciones')
                    ->label('Observaciones')
                    ->rows(3)
                    ->maxLength(500)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('producto_id')
            ->columns([
                TextColumn::make('bodegaDonante.nombre_bodega')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('bodegaDestino.nombre_bodega')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('producto.concatenar_codigo_nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable()
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
                SelectFilter::make('bodega_donante_id')
                    ->relationship('bodegaDonante', 'nombre_bodega'),
                SelectFilter::make('bodega_destino_id')
                    ->relationship('bodegaDestino', 'nombre_bodega'),
                SelectFilter::make('producto_id')
                    ->relationship('producto', 'nombre_producto'),
            ])
            ->recordActions([
                //EditAction::make(),
                ViewAction::make(),
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
            'index' => ManageTraslados::route('/'),
        ];
    }
}
