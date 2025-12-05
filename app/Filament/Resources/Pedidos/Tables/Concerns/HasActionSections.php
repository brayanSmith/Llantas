<?php

namespace App\Filament\Resources\Pedidos\Tables\Concerns;

use App\Models\Abono;
use App\Models\Pedido;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

trait HasActionSections
{
    public static function registrarAbonoAction(): Action
    {
        return Action::make('crearAbono')
            ->label('Abonar')
            ->icon('heroicon-o-currency-dollar')
            ->modalHeading('Registrar Nuevo Abono')
            //->modalWidth('md')
            ->form([
                DateTimePicker::make('fecha')->label('Fecha')->required()->default(now())->columnSpan(1),
                TextInput::make('monto')->label('Monto')->prefix('$')->inputMode('decimal')->currencyMask('.', ',', 0)->required()->stripCharacters('.')->live(onBlur: true)->numeric()->columnSpan(1),
                Select::make('forma_pago')
                    ->label('Forma de pago')
                    ->relationship(
                        name: 'abonoPedido.formaPago',
                        titleAttribute: 'concatenar_subcuenta_concepto',
                        modifyQueryUsing: fn ($query) => $query->where('tipo', 1)
                    )
                    ->searchable()
                    ->required()
                    ->preload()
                    ->reactive()
                    ->columnSpan(1),
                Textarea::make('descripcion')->label('Descripción')->default(null)->columnSpan(2),
                Select::make('user_id')
                    ->label('Usuario que registra')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(1),
                
                FileUpload::make('imagen')
                    ->label('Comprobante o evidencia')
                    ->directory('abonos')
                    ->image()
                    ->imagePreviewHeight('200')
                    ->acceptedFileTypes(['image/*'])
                    ->columnSpanFull(),                    
            ])
            ->action(function (Pedido $record, array $data): void {
                // Lógica para registrar el abono
                Abono::create([
                    'fecha' => $data['fecha'],
                    'monto' => $data['monto'],
                    'descripcion' => $data['descripcion'] ?? null,
                    'pedido_id' => $record->id,
                    'forma_pago' => $data['forma_pago'],
                    'user_id' => $data['user_id'],
                    'imagen' => $data['imagen'] ?? null,
                ]);
            });
    }
}
