<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoPDFPendienteController extends Controller
{
    public function download($id) 
    {
        $pedido = Pedido::with(['cliente', 'detalles.producto'])->findOrFail($id);

        // incrementar contador de impresiones de forma atómica
        $pedido->increment('contador_impresiones');

        // Ordenar detalles por ubicación y código de producto
        $detallesOrdenados = $pedido->detalles->sortBy([
            ['producto.ubicacion', 'asc'],
            ['producto.codigo_producto', 'asc']
        ]);

        $pdf = Pdf::loadView('pdf.pedido', [
            'pedido'   => $pedido,
            'cliente'  => $pedido->cliente,
            'detalles' => $detallesOrdenados,
        ]);

        return $pdf->download("pedido_{$pedido->id}.pdf");
    }

    public function stream($id)
    {
        $pedido = Pedido::with(['cliente', 'detalles.producto'])->findOrFail($id);

        // incrementar contador de impresiones de forma atómica
        $pedido->increment('contador_impresiones');

        // Ordenar detalles por ubicación y código de producto
        $detallesOrdenados = $pedido->detalles->sortBy([
            ['producto.ubicacion', 'asc'],
            ['producto.codigo_producto', 'asc']
        ]);

        $pdf = Pdf::loadView('pdf.pedido', [
            'pedido'   => $pedido,
            'cliente'  => $pedido->cliente,
            'detalles' => $detallesOrdenados,
        ]);

        return $pdf->stream("pedido_{$pedido->id}.pdf");
    }
}
