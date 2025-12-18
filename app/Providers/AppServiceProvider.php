<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Compra;
use App\Models\Pedido;
use App\Models\DetalleCompra;
use App\Models\DetallePedido;
use App\Models\Traslado;
use App\Models\AbonoCompra;
use App\Models\Producto;
use App\Models\Produccion;
use App\Models\DetalleProduccionSalida;
use App\Models\DetalleProduccionEntrada;

use App\Observers\CompraObserver;
use App\Observers\CompraStockBodegaObserver;
use App\Observers\DetalleCompraObserver;
use App\Observers\AbonoCompraObserver;
use App\Observers\ProduccionObserver;
use App\Observers\DetalleProduccionSalidaObserver;
use App\Observers\DetalleProduccionEntradaObserver;

use App\Observers\PedidoObserver;
use App\Observers\PedidoStockBodegaObserver;
use App\Observers\DetallePedidoObserver;

use App\Observers\TrasladoObserver;
use App\Observers\ProductoObserver;

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
        Producto::observe(ProductoObserver::class);
        
        Traslado::observe(TrasladoObserver::class);
        DetalleCompra::observe(DetalleCompraObserver::class);
        AbonoCompra::observe(AbonoCompraObserver::class);
        Compra::observe(CompraStockBodegaObserver::class);

        DetallePedido::observe(DetallePedidoObserver::class);
        Pedido::observe(PedidoStockBodegaObserver::class);    
        
        Produccion::observe(ProduccionObserver::class);
        DetalleProduccionSalida::observe(DetalleProduccionSalidaObserver::class);
        DetalleProduccionEntrada::observe(DetalleProduccionEntradaObserver::class);
    }
}
