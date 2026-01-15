<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Pedido\PedidoEstados;
use App\Models\Pedido;

class PedidoEstadoCarteraCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedido:pedido-estado-cartera-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y actualiza el estado de cartera de los pedidos según su estado actual.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pedidos = Pedido::all();
        foreach ($pedidos as $pedido) {
            PedidoEstados::actualizarEstadoCartera($pedido);
        }
    }
}
