<?php

namespace App\Filament\Resources\DetallePedidos;

use App\Filament\Resources\DetallePedidos\Pages\ManageDetallePedidos;
use App\Models\DetallePedido;
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
            ->recordTitleAttribute('pedido_id')
            ->columns([
                TextColumn::make('pedido_id')
                    ->searchable(),
                TextColumn::make('producto.concatenar_codigo_nombre')
                    ->searchable(),
                TextColumn::make('cantidad'),
                TextColumn::make('precio_unitario'),
                TextColumn::make('costo_unitario'),
                TextColumn::make('costo_total'),
                TextColumn::make('ganancia_total'),
                TextColumn::make('subtotal'),
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
            'index' => ManageDetallePedidos::route('/'),
        ];
    }
}
