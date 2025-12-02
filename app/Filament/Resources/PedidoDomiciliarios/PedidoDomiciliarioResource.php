<?php

namespace App\Filament\Resources\PedidoDomiciliarios;

use App\Filament\Resources\PedidoDomiciliarios\Pages\ManagePedidoDomiciliarios;
use App\Models\Pedido;
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
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Select;
use Filament\Actions\ViewAction;

class PedidoDomiciliarioResource extends Resource
{
    protected static ?string $model = Pedido::class;
    
    // Labels personalizados para Shield
    protected static ?string $modelLabel = 'Pedido Domiciliario';
    protected static ?string $pluralModelLabel = 'Pedidos Domiciliarios';
    protected static ?string $navigationLabel = 'Domiciliarios';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:PedidoDomiciliarioResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:PedidoDomiciliarioResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:PedidoDomiciliarioResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:PedidoDomiciliarioResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:PedidoDomiciliarioResource');
    }

    public static function getNavigationLabel(): string
    {
        return 'Domicilios';
    }
    public static function getPluralLabel(): string
    {
        return 'Domicilios';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo')
                    ->required()
                    ->label('Código')
                    ->disabled()
                    ->maxLength(255),
                ToggleButtons::make('estado')
                    ->options([
                        'ENTREGADO' => 'Entregado',
                        'DEVUELTO' => 'Devuelto',                        
                    ])
                    ->colors([
                        'ENTREGADO' => 'success',
                        'DEVUELTO' => 'danger',                        
                    ])
                    ->grouped()
                    ->label('Estado')
                    ->reactive()
                    ->required(),
                FileUpload::make('imagen_recibido')
                    ->image()
                    ->label('Imagen de recibido')
                    ->required(fn($get) => $get('estado') === 'ENTREGADO')
                    ->downloadable()                    
                    ->maxSize(1024),
                Select::make('motivo_devolucion')
                    ->label('Motivo de devolución')
                    ->required(fn($get) => $get('estado') === 'DEVUELTO')
                    ->visible(fn($get) => $get('estado') === 'DEVUELTO')
                    ->options([
                        'CERRADO' => 'Cerrado',
                        'TRASLADO' => 'Traslado',
                        'NO_CANCELA' => 'No cancela',
                        'NO_RECIBE' => 'No recibe',
                    ]),
                    
                TextArea::make('comentario_entrega')
                    ->label('Comentario de entrega')
                    ->maxLength(500),

            ]);
    }


   
    //funcion para mostrar la tabla de los pedidos domiciliarios

    public static function table(Table $table): Table
    {
        return $table
        //solo se van a mostrar los pedidos que tengan estado EN_RUTA
            ->modifyQueryUsing(fn ($query) => $query->where('estado', 'EN_RUTA'))
        
            ->recordTitleAttribute('codigo')
            ->defaultGroup('cliente.ruta.ruta')
            ->columns([
                Split::make([   
                TextColumn::make('cliente.ruta.ruta')
                    ->label('Ruta')
                    ->weight(FontWeight::Bold)
                    ->getStateUsing(function ($record) {
                        $ruta = $record->cliente?->ruta?->ruta;
                        return $ruta ? "Ruta: {$ruta}" : 'Sin ruta';
                    })
                    
                    ->searchable()
                    ->sortable(),
                TextColumn::make('codigo')
                    ->getStateUsing(function ($record) {
                       $codigo = $record->codigo;
                       return $codigo ? "Código: {$codigo}" : 'Sin código';
                    })
                    ->searchable(),
                TextColumn::make('total_a_pagar')
                    ->label('Total')
                    ->money('COP', decimalPlaces: 0) 
                    ->weight(FontWeight::Bold),                   
                    
                TextColumn::make('cliente.razon_social')
                    ->getStateUsing(function ($record) {
                        $cliente = $record->cliente?->razon_social;
                        return $cliente ? "Cliente: {$cliente}" : 'Sin cliente';
                    })
                    ->label('Cliente')
                    ->searchable(),                
                TextColumn::make('estado')
                    ->badge()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->colors([
                        'ENTREGADO' => 'success',
                        'DEVUELTO' => 'danger',                        
                    ])
                    ->searchable(),
                    
            ])->from('md')
            ])

            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn($record) => static::canView($record)),
                EditAction::make()
                    ->visible(fn($record) => static::canEdit($record)),
                DeleteAction::make()
                    ->visible(fn($record) => static::canDelete($record)),
            ])
            ->toolbarActions([
                /*BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),*/
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePedidoDomiciliarios::route('/'),
        ];
    }
}
