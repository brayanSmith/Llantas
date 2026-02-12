<?php

namespace App\Filament\Resources\PedidosFacturados\Tables;

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
use Filament\Tables\Columns\ActionColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Enums\RecordActionsPosition;
use App\Filament\Tables\Columns\DescargarPdfColumn;
use App\Filament\Resources\Pedidos\Tables\HasPedidoTable;
use App\Filament\Resources\Pedidos\Tables\Concerns\HasActionSections;

class PedidosFacturadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->where('estado', 'FACTURADO')
                ->where('estado_venta', 'VENTA');

                // Si el usuario no es super_admin, mostrar solo sus pedidos
                if (!auth()->user()->hasRole(['super_admin','financiero' ])) {
                    $query->where('user_id', auth()->id());
                }

                return $query;
            })
            ->groups([
                Group::make('fecha')
                    ->date()
                    ->collapsible(),
                Group::make('cliente.ruta.ruta')
                    ->collapsible(),

            ])->defaultGroup('fecha')
            ->columns([
                //
                ...HasPedidoTable::tableColumns(),
            ])
            ->filters([
                //
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

                // Filtro por Vendedor (solo visible para super_admin)
                SelectFilter::make('user_id')
                    ->label('Vendedor')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->visible(fn() => auth()->user()->hasRole('super_admin')),
            ])
             ->recordActions([
                ActionGroup::make([
                    HasActionSections::registrarAbonoAction(),
                    ViewAction::make()
                        ->modalWidth('full'),
                    Action::make('edit')
                            ->label('Editar')
                            ->icon('heroicon-o-pencil')
                            ->url(fn($record) => route('filament.admin.resources.pedidos-facturados.edit', ['record' => $record->getKey(), 'pedido_id' => $record->getKey()]))
                            ->openUrlInNewTab(false),
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
