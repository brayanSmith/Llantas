<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoPDFPendienteController extends Controller
{
    public function download($id)
    {
        $pedido = Pedido::with(['cliente', 'detalles.producto'])->findOrFail($id);

        // Ordenar detalles por ubicación y código de producto
        $detallesOrdenados = $pedido->detalles->sortBy([
            ['producto.ubicacion_producto', 'asc'],
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


        // Ordenar detalles por ubicación y código de producto
        $detallesOrdenados = $pedido->detalles->sortBy([
            ['producto.ubicacion_producto', 'asc'],
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
