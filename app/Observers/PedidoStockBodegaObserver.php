<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Services\PedidoStockService;
use App\Services\PedidoCalculoService;
use App\Models\Cliente;

class PedidoStockBodegaObserver
{

    public function creating(Pedido $pedido): void
    {
        // No hacer nada aquí, el pedido aún no tiene ID
    }

    /**
     * Handle the Pedido "created" event.
     */
    public function created(Pedido $pedido): void
    {
        $codigo = $pedido->setCodigoPedido();
        $estadoPago = $pedido->setEstadoPago();
        //$totales = $pedido->recalcularTotales();
        app(PedidoStockService::class)->creado($pedido);

        // Actualizar vendedor de los abonos
        PedidoCalculoService::actualizarVendedorAbonos($pedido);

        // Actualizar totales de pedidos en cartera del cliente
        $cliente = Cliente::find($pedido->cliente_id);
        PedidoCalculoService::setPedidosEnCarteraTotales($cliente, $pedido->cliente_id);
    }

    /**
     * Handle the Pedido "updated" event.
     */
    public function updated(Pedido $pedido): void
    {
        $pedido->recalcularTotales();
        $estadoPago = $pedido->setEstadoPago();
        app(PedidoStockService::class)->actualizado($pedido);

        // Actualizar vendedor de los abonos si cambió
        PedidoCalculoService::actualizarVendedorAbonos($pedido);

        // Actualizar totales de pedidos en cartera del cliente
         $cliente = Cliente::find($pedido->cliente_id);
        PedidoCalculoService::setPedidosEnCarteraTotales($cliente, $pedido->cliente_id);
    }

    /**
     * Handle the Pedido "deleted" event.
     */
    public function deleting(Pedido $pedido): void
    {
        //
        app(PedidoStockService::class)->eliminado($pedido);
        // Actualizar totales de pedidos en cartera del cliente
         $cliente = Cliente::find($pedido->cliente_id);
        PedidoCalculoService::setPedidosEnCarteraTotales($cliente, $pedido->cliente_id);
    }

}
