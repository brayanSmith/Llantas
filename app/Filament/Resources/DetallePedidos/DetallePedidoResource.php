<?php

namespace App\Filament\Resources\DetallePedidos;

use App\Filament\Resources\DetallePedidos\Pages\ManageDetallePedidos;
use App\Models\DetallePedido;
use BackedEnum;
use Dom\Text;
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

class DetallePedidoResource extends Resource
{
    protected static ?string $model = DetallePedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pedido_id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pedido_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([25, 50, 100]) // Opciones de paginación
            ->defaultPaginationPageOption(100) // Por defecto 100 registros por página
            ->recordTitleAttribute('pedido_id')
            ->groups([
                \Filament\Tables\Grouping\Group::make('pedido.fecha')
                    ->label('Fecha del Pedido')
                    ->date()
                    ->collapsible(),
            ])
            ->columns([


                TextColumn::make('stock_total')
                    ->label('Stock Total')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->producto?->stockBodegas->sum('stock') ?? 0)
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 3 ? 'success' : ($state > 0 ? 'warning' : 'danger'))),
                /*TextColumn::make('pedido_id')
                    ->searchable(),*/
                TextColumn::make('producto.concatenar_codigo_nombre')
                    ->searchable(),

                TextColumn::make('pedido.fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('cantidad')
                    ->sortable(),
                TextColumn::make('precio_unitario')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('costo_unitario')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('costo_total')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('ganancia_total')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            /*->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])*/
            ->toolbarActions([
                /*BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),*/
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDetallePedidos::route('/'),
        ];
    }
}
