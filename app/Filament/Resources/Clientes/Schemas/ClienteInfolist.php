<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use App\Models\Cliente;


class ClienteInfolist
{
    private static function getPedidosRepeatable(string $nombre, array $filtros = []): RepeatableEntry
    {
        return RepeatableEntry::make($nombre)
            ->label('Pedidos')
            ->columns(3)
            ->table([
                TableColumn::make('Codigo'),
                TableColumn::make('Fecha Vencimiento'),
                TableColumn::make('Saldo Pendiente'),
                TableColumn::make('Estado Cartera'),
                TableColumn::make('Abono'),
                TableColumn::make('Total A Pagar'),
            ])
            ->schema([
                TextEntry::make('codigo')
                    ->label('Pedido')
                    ->icon('heroicon-o-hashtag'),
                TextEntry::make('fecha_vencimiento')
                    ->label('Vence El')
                    ->icon('heroicon-o-calendar'),
                TextEntry::make('saldo_pendiente')
                    ->label('Saldo')
                    ->icon('heroicon-o-currency-dollar')
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.')),
                TextEntry::make('estado_vencimiento')
                    ->label('Estado')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color(fn ($state) => $state === 'VENCIDO' ? 'danger' : 'success'),
                TextEntry::make('abono')
                    ->label('Abonos')
                    ->icon('heroicon-o-currency-dollar')
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.')),
                TextEntry::make('total_a_pagar')
                    ->label('Total')
                    ->icon('heroicon-o-currency-dollar')
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.')),
            ])
            ->getStateUsing(function ($record) use ($filtros) {
                $query = $record->pedidos();

                if (isset($filtros['estado_pago'])) {
                    $query->where('estado_pago', $filtros['estado_pago']);
                }

                if (isset($filtros['estados'])) {
                    $query->whereIn('estado', $filtros['estados']);
                }

                return $query->get();
            });
    }

    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([
                Tabs::make()
                ->columnSpanFull()
                    ->tabs([
                        Tab::make('Datos Cliente')
                            ->schema([
                                TextEntry::make('tipo_documento')
                                    ->label('Tipo Documento'),
                                TextEntry::make('numero_documento')
                                    ->label('Número Documento'),
                                TextEntry::make('razon_social')
                                    ->label('Razón Social')
                                    ->columnSpanFull(),
                                TextEntry::make('direccion')
                                    ->label('Dirección')
                                    ->columnSpanFull(),
                                TextEntry::make('telefono')
                                    ->label('Teléfono'),
                                TextEntry::make('ciudad')
                                    ->label('Ciudad'),
                            ])
                            ->columns(2),
                        Tab::make('Pedidos En Cartera')
                            ->badge(fn ($record) => $record->pedidos()
                            ->where('estado_pago', 'EN_CARTERA')
                             ->whereIn('estado', ['FACTURADO', 'EN_RUTA', 'ENTREGADO'])
                            ->count())
                            ->icon('heroicon-o-clock')
                            ->schema([
                                self::getPedidosRepeatable('estadoPagoEnCartera', [
                                    'estado_pago' => 'EN_CARTERA',
                                    'estados' => ['FACTURADO', 'EN_RUTA', 'ENTREGADO']
                                ]),
                            ]),
                        Tab::make('Pedidos Saldados')
                            ->badge(fn ($record) => $record->pedidos()->where('estado_pago', 'SALDADO')->count())
                            ->icon('heroicon-o-clock')
                            ->schema([
                                self::getPedidosRepeatable('estadoPagoSaldados', [
                                    'estado_pago' => 'SALDADO'
                                ]),
                            ]),
                    ])
                     ->vertical(fn () => !\Jenssegers\Agent\Facades\Agent::isMobile()),

            ]);
    }
}
