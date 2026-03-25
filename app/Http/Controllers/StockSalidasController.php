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
        $request->validate([
            'productos' => ['required', 'array'],
            'productos.*.producto_id' => ['required'],
            'productos.*.bodega_id' => ['nullable'],
            'bodega_id' => ['nullable'],
        ]);

        $productos = $request->input('productos');
        $bodegaId = $request->input('bodega_id');
        $bodegaBroadcastId = (int)($bodegaId ?? 0);

        if (!$bodegaBroadcastId) {
            foreach ($productos as $item) {
                $candidateBodegaId = (int)($item['bodega_id'] ?? 0);
                if ($candidateBodegaId > 0) {
                    $bodegaBroadcastId = $candidateBodegaId;
                    break;
                }
            }
        }

        $resultados = [];

        $servicio = new \App\Services\StockCalculoService();


        foreach ($productos as $item) {
            $productoId = (int)$item['producto_id'];
            $bodegaItemId = (int)($item['bodega_id'] ?? $bodegaBroadcastId);

            // Skip malformed items instead of throwing runtime notices.
            if (!$productoId || !$bodegaItemId) {
                continue;
            }

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

        if (!empty($resultados) && $bodegaBroadcastId > 0) {
            broadcast(new StockActualizado($resultados, $bodegaBroadcastId))->toOthers();
        }

        return response()->json(['status' => 'ok']);
    }
}
