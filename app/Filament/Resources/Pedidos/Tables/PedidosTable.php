<?php

namespace App\Filament\Resources\Pedidos\Tables;

use App\Filament\Traits\HasEditarAction;
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
    use HasEditarAction;
    public static function configure(Table $table): Table
    {
        $instance = new self();

        return $table
        ->striped()
            ->modifyQueryUsing(function ($query) {
                //$query->where('estado_venta', 'VENTA');
                // Si el usuario no es super_admin, mostrar solo sus pedidos
                if (!auth()->user()->hasRole(['super_admin','Financiero','Logistica'])) {
                    $query->where('user_id', auth()->id());
                }
                return $query;
            })
            ->defaultSort('created_at', 'desc')  // ← AQUÍ

            ->groups([
                /*Group::make('fecha')
                    ->date()
                    ->collapsible(),*/
            ])

            ->columns([
                ...HasPedidoTable::tableColumns(),
            ])
            ->filters([
                // Filtro por Ruta


                // Filtro por Cliente
                SelectFilter::make('cliente_id')
                    ->label(label: 'Cliente')
                    ->relationship('cliente', 'razon_social')
                    ->searchable()
                    ->preload()
                    ->multiple(),


            ])
            ->recordActions([
                // Solo la acción de editar personalizada
                $instance->getEditarAction('filament.admin.resources.pedidos.edit'),
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
