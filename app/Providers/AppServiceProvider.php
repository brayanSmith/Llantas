<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Compra;
use App\Models\Pedido;
use App\Models\DetalleCompra;
use App\Models\DetallePedido;
use App\Models\Traslado;

use App\Observers\CompraObserver;
use App\Observers\CompraStockBodegaObserver;
use App\Observers\DetalleCompraObserver;

use App\Observers\PedidoObserver;
use App\Observers\PedidoStockBodegaObserver;
use App\Observers\DetallePedidoObserver;

use App\Observers\TrasladoObserver;

use App\Policies\PedidoCarteraPolicy;
use App\Policies\PedidoSaldadoPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Traslado::observe(TrasladoObserver::class);
        DetalleCompra::observe(DetalleCompraObserver::class);        
        Compra::observe(CompraStockBodegaObserver::class);

        DetallePedido::observe(DetallePedidoObserver::class);
        Pedido::observe(PedidoStockBodegaObserver::class);
      
        Compra::observe(CompraObserver::class);        
        Pedido::observe(PedidoObserver::class);
   
    }
}
