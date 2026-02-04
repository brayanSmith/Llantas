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
use LaravelLang\Publisher\Concerns\Has;

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
            ])->defaultGroup('cliente.ruta.ruta')

            ->columns([
                ...HasPedidoTable::tableColumns(),
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
