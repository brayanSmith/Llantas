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
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Columns\ImageColumn;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'codigo_gasto';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo_gasto')
                    ->required(),
                TextInput::make('concepto_gasto')
                    ->required(),
                Textarea::make('descripcion_gasto')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('monto_gasto')
                    ->required()
                    ->prefix('$')
                    ->inputMode('decimal')
                    ->currencyMask(".", ",", 0)
                    ->numeric(),
                DatePicker::make('fecha_gasto')
                    ->required(),
                TextInput::make('cuenta_gasto')
                    ->required(),
                TextInput::make('subcuenta_gasto')
                    ->default(null),                

                FileUpload::make('comprobante_gasto')
                    ->label('Seleccione una imagen')
                    ->image()
                    ->directory('comprobantes_gastos')
                    ->disk('public')
                    ->imageEditor()
                    ->downloadable()
                    ->openable()
                    ->nullable()
                    ->maxSize(1024) // 1MB
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo_gasto')
            ->columns([
                ImageColumn::make('comprobante_gasto'),
                    
                TextColumn::make('codigo_gasto')
                    ->searchable(),
                TextColumn::make('concepto_gasto')
                    ->searchable(),
                TextColumn::make('monto_gasto')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fecha_gasto')
                    ->date()
                    ->sortable(),
                TextColumn::make('cuenta_gasto')
                    ->searchable(),
                TextColumn::make('subcuenta_gasto')
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
                //DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
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
