<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoPDFacturadoController extends Controller
{
    public function download($id)
    {
        $pedido = Pedido::with(['cliente', 'detalles.producto'])->findOrFail($id);
        $empresa = Empresa::first(); // Obtener el primer registro de empresas

        // Ordenar detalles por ubicación y código de producto
        $detallesOrdenados = $pedido->detalles->sortBy([
            ['producto.ubicacion_producto', 'asc'],
            ['producto.codigo_producto', 'asc']
        ]);

        $pdf = Pdf::loadView('pdf.pedidoFacturado', [
            'pedido'   => $pedido,
            'cliente'  => $pedido->cliente,
            'detalles' => $detallesOrdenados,
            'empresa'  => $empresa,
        ]);

        return $pdf->download("pedidoFacturado_{$pedido->id}.pdf");
    }

    public function stream($id)
    {
        $pedido = Pedido::with(['cliente', 'detalles.producto'])->findOrFail($id);
        $empresa = Empresa::first(); // Obtener el primer registro de empresas

        // Ordenar detalles por ubicación y código de producto
        $detallesOrdenados = $pedido->detalles->sortBy([
            ['producto.ubicacion', 'asc'],
            ['producto.codigo_producto', 'asc']
        ]);

        $pdf = Pdf::loadView('pdf.pedidoFacturado', [
            'pedido'   => $pedido,
            'cliente'  => $pedido->cliente,
            'detalles' => $detallesOrdenados, 
            'empresa'  => $empresa,
        ]);

        return $pdf->stream("pedido_Facturado{$pedido->id}.pdf");
    }
}