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

    protected static ?string $recordTitleAttribute = 'sub_cuenta_gasto';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cuenta_gasto')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($set, $get) {
                        // actualizar el campo concatenado
                        $cuenta = $get('cuenta_gasto');                        
                        $set('subcuenta_gasto', $cuenta);
                    }),
                TextInput::make('subcuenta_gasto')
                    ->required()
                    ->live(onBlur: true)
                    ->unique(ignoreRecord: true)
                    //->reactive()
                    ->afterStateUpdated(function ($set, $get) {
                        // actualizar el campo concatenado
                        $concatenado = $get('subcuenta_gasto') . ' - ' . $get('concepto_gasto');
                        $set('concatenar_subcuenta_concepto', $concatenado);
                    }),
                TextInput::make('concepto_gasto')
                    ->live(onBlur: true)
                    ->required()
                    //->reactive()
                    ->afterStateUpdated(function ($set, $get) {
                        // actualizar el campo concatenado
                        $concatenado = $get('subcuenta_gasto') . ' - ' . $get('concepto_gasto');
                        $set('concatenar_subcuenta_concepto', $concatenado);
                    }),
                Textarea::make('descripcion_gasto')
                    ->rows(3),
                TextInput::make('concatenar_subcuenta_concepto')
                    //el valor se genera concatenando subcuenta y concepto
                    //->disabled()
                    //->value(fn (callable $get) => $get('subcuenta_gasto') . ' - ' . $get('concepto_gasto'))
                    ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo_gasto')
            ->columns([
                TextColumn::make('cuenta_gasto')->label('Cuenta de Gasto')->sortable()->searchable(),
                TextColumn::make('subcuenta_gasto')->label('Subcuenta de Gasto')->sortable()->searchable(),
                TextColumn::make('concepto_gasto')->label('Concepto de Gasto')->sortable()->searchable(),
                TextColumn::make('descripcion_gasto')->label('Descripción del Gasto')->sortable()->searchable(),
                TextColumn::make('concatenar_subcuenta_concepto')->label('Subcuenta - Concepto')->sortable()->searchable(),
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
