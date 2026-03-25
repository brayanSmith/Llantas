<?php

namespace App\Filament\Resources\StockBodegas;

use App\Filament\Resources\StockBodegas\Pages\ManageStockBodegas;
use App\Models\StockBodega;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Bodega;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Filament\Traits\HasTrasladosSection;
use App\Filament\Traits\HasProductosStockSection;


class StockBodegaResource extends Resource
{
    protected static ?string $model = StockBodega::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'producto.concatenar_codigo_nombre';



    public static function table(Table $table): Table
    {
        $columns = [
            TextColumn::make('concatenar_codigo_nombre')
                ->label('Producto')
                ->searchable()
                ->sortable()
                ->wrap(),
        ];

        // Generar columnas dinámicas por cada bodega
        $bodegas = Bodega::orderBy('nombre_bodega')->get();

        foreach ($bodegas as $bodega) {
            $columns[] = TextColumn::make("stock_bodega_{$bodega->id}")
                ->label($bodega->nombre_bodega)
                ->alignCenter()
                ->getStateUsing(function ($record) use ($bodega) {
                    $stock = $record->stockBodegas()
                        ->where('bodega_id', $bodega->id)
                        ->first();
                    return $stock ? $stock->stock : 0;
                })
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'success' : 'danger');
        }

        return $table
            ->query(
                \App\Models\Producto::query()
                    ->with('stockBodegas')
                    ->whereHas('stockBodegas')
            )
            ->columns($columns)
            ->filters([
                //
            ])
            ->recordActions([
                HasTrasladosSection::getTrasladosSection(),
                HasProductosStockSection::getProductosStockSection(),

            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStockBodegas::route('/'),
        ];
    }
}
