<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Compra;
use App\Models\Pedido;
use App\Models\DetalleCompra;
use App\Observers\CompraObserver;
use App\Observers\PedidoObserver;
use App\Observers\DetalleCompraObserver;
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
        // Instanciamos manualmente el observer de DetalleCompra
        //$detalleObserver = new DetalleCompraObserver();

        // Registramos los observers en su orden lógico
        Compra::observe(CompraObserver::class);
        //DetalleCompra::observe(DetalleCompraObserver::class);
        Pedido::observe(PedidoObserver::class);
    }
}
