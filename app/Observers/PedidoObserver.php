<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoObserver
{
    //
    public function saved(Pedido $pedido) 
    {
        
    }

    public function updated(Pedido $pedido): void
    {
       
    }
}
