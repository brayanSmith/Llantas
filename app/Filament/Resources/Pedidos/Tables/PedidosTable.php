<?php

namespace App\Filament\Resources\Pedidos\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Enums\RecordActionsPosition;
use App\Filament\Tables\Columns\DescargarPdfColumn;
use App\Filament\Resources\Pedidos\Tables\Concerns\HasActionSections;

class PedidosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('fecha')
                    ->date()
                    ->collapsible(),
                Group::make('cliente.ruta.ruta')
                    ->collapsible(),


            ])->defaultGroup('fecha')
            ->columns([
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDIENTE' => 'warning',
                        'FACTURADO' => 'success',
                        'ANULADO' => 'danger',
                        default => 'primary',
                    }),
                TextColumn::make('fecha')
                    ->label('Fecha de Facturación')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('codigo')
                    ->label('Remisión')
                    ->searchable()
                    ->sortable(),
                DescargarPdfColumn::make('descargar_pdf')
                    ->label('Pdf'),
                TextColumn::make('cliente.razon_social')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cliente.ruta.ruta')
                    ->label('Ruta')
                    ->sortable(),

                TextColumn::make('subtotal')
                    ->numeric(0)
                    ->sortable(),

                TextColumn::make('abono')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('descuento')
                    ->numeric(0)
                    ->sortable(),
                TextColumn::make('total_a_pagar')
                    ->label('Total a Pagar')
                    ->numeric(0)
                    ->sortable(),

                ToggleColumn::make('impresa')
                    ->label('Impresa'),

                TextColumn::make('tipo_venta')
                    ->label('Tipo Venta'),

                TextColumn::make('ciudad')
                    ->searchable(),

                TextColumn::make('metodo_pago')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'EFECTIVO' => 'success',
                        'A CREDITO' => 'info',
                        default => 'secondary',
                    }),
                TextColumn::make('tipo_precio')
                    ->badge(),
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
                // Filtro por Ruta
                SelectFilter::make('cliente.ruta_id')
                    ->label('Ruta')
                    ->relationship('cliente.ruta', 'ruta')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Filtro por Cliente
                SelectFilter::make('cliente_id')
                    ->label(label: 'Cliente')
                    ->relationship('cliente', 'razon_social')
                    ->searchable()
                    ->preload()
                    ->multiple(),


            ])
            ->recordActions([
                ActionGroup::make([
                    HasActionSections::registrarAbonoAction(),
                    ViewAction::make()
                        ->modalWidth('full'),
                    EditAction::make(),
                    Action::make('download_pdf')
                        ->label(fn ($record) => 'Descargar PDF (' . ($record->contador_impresiones ?? 0) . ')')
                        //->icon('heroicon-o-document-download')
                        ->url(fn ($record) => route('pedidos.pdf.download', $record->id))
                        ->openUrlInNewTab(),
                    Action::make('download_pdf_facturado')
                        ->label('Descargar PDF Facturado')
                        //->icon('heroicon-o-document-download')
                        ->url(fn ($record) => route('pedidosFacturados.pdf.download', $record->id))
                        ->openUrlInNewTab(),
                ]),
            ],
            position: RecordActionsPosition::BeforeColumns
        )
            ->toolbarActions([
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
                ]),
            ]);
    }
}
