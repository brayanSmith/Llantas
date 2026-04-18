<?php

namespace App\Filament\Resources\Pucs;

use App\Filament\Resources\Pucs\Pages\ManagePucs;
use App\Models\Puc;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Actions\ViewAction;
use unitEnum;

class PucResource extends Resource
{
    protected static ?string $model = Puc::class;
     protected static ?string $modelLabel = 'Medios de Pago';
    protected static ?string $pluralModelLabel = 'Medios de Pago';
    protected static ?string $navigationLabel = 'Medios de Pago';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;
    protected static string | UnitEnum | null $navigationGroup = 'Sistema';


    protected static ?string $recordTitleAttribute = 'concatenar_subcuenta_concepto';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->options([
                        '1' => 'Activo',
                        '2' => 'Pasivo',
                        '3' => 'Patrimonio',
                        '4' => 'Ingresos',
                        '5' => 'Gastos',
                        '6' => 'Costos',
                        '7' => 'Costos de Producción u Operación',
                        '8' => 'Cuentas de Orden deudoras',
                        '9' => 'Cuentas de orden acreedoras',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($set, $get) {
                        // actualizar el campo concatenado
                        $tipo = $get('tipo');
                        $set('cuenta', $tipo);
                    })
                    ->required(),
                TextInput::make('cuenta')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($set, $get) {
                        // actualizar el campo concatenado
                        $cuenta = $get('cuenta');
                        $set('subcuenta', $cuenta);
                    }),
                TextInput::make('subcuenta')
                    ->required()
                    ->live(onBlur: true)
                    ->unique(ignoreRecord: true)
                    //->reactive()
                    ->afterStateUpdated(function ($set, $get) {
                        // actualizar el campo concatenado
                        $concatenado = $get('subcuenta') . ' - ' . $get('concepto');
                        $set('concatenar_subcuenta_concepto', $concatenado);
                    }),
                TextInput::make('concepto')
                    ->live(onBlur: true)
                    ->required()
                    //->reactive()
                    ->afterStateUpdated(function ($set, $get) {
                        // actualizar el campo concatenado
                        $concatenado = $get('subcuenta') . ' - ' . $get('concepto');
                        $set('concatenar_subcuenta_concepto', $concatenado);
                    }),
                Textarea::make('descripcion')
                    ->rows(3),
                TextInput::make('concatenar_subcuenta_concepto')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('concatenar_subcuenta_concepto')
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    //de acuerdo al valor mostrar el texto correspondiente
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            '1' => 'Activo',
                            '2' => 'Pasivo',
                            '3' => 'Patrimonio',
                            '4' => 'Ingresos',
                            '5' => 'Gastos',
                            '6' => 'Costos',
                            '7' => 'Costos de Producción u Operación',
                            '8' => 'Cuentas de Orden deudoras',
                            '9' => 'Cuentas de orden acreedoras',
                            default => 'Desconocido',
                        };
                    })
                    ->badge(),
                TextColumn::make('cuenta')
                    ->searchable(),
                TextColumn::make('subcuenta')
                    ->searchable(),
                TextColumn::make('concepto')
                    ->searchable(),
                TextColumn::make('descripcion')
                    ->searchable(),
                TextColumn::make('concatenar_subcuenta_concepto')
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
            'index' => ManagePucs::route('/'),
        ];
    }
}
