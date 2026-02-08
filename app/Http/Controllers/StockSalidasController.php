<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\StockActualizado;

class StockSalidasController extends Controller
{
    //
    public function index()
    {
        return view('stock_salidas.index');
    }

    public function recalcularStockArray(Request $request)
    {
        $productos = $request->input('productos');
        $bodegaId = $request->input('bodega_id');
        $resultados = [];

        $servicio = new \App\Services\StockCalculoService();


        foreach ($productos as $item) {
            $productoId = (int)$item['producto_id'];
            $bodegaItemId = (int)$item['bodega_id'];
            $servicio->recalcularStockPorProductoYBodega($productoId, $bodegaItemId);
            $stock = \App\Models\StockBodega::where('producto_id', $productoId)
                ->where('bodega_id', $bodegaItemId)
                ->value('stock');
            $resultados[] = [
                'producto_id' => $productoId,
                'bodega_id' => $bodegaItemId,
                'stock' => $stock,
            ];
        }

        broadcast(new StockActualizado($resultados, $bodegaId))->toOthers();

        return response()->json(['status' => 'ok']);
    }
}
