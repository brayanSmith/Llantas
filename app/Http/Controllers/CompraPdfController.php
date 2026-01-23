<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraPdfController extends Controller
{
    //
    public function download($id)
    {
        $compra = Compra::with(['proveedor', 'detallesCompra'])->findOrFail($id);
        $empresa = Empresa::first(); // Obtener el primer registro de empresas

        $pdf = Pdf::loadView('pdf.compra', [
            'compra'   => $compra,
            'proveedor'  => $compra->proveedor,
            'detallesCompra' => $compra->detallesCompra,
            'empresa'  => $empresa,
        ]);

        return $pdf->download("compra_{$compra->id}.pdf");
    }

    public function stream($id)
    {
        $compra = Compra::with(['proveedor', 'detallesCompra'])->findOrFail($id);
        $empresa = Empresa::first(); // Obtener el primer registro de empresas
        $pdf = Pdf::loadView('pdf.compra', [
            'compra'   => $compra,
            'proveedor'  => $compra->proveedor,
            'detallesCompra' => $compra->detallesCompra,
            'empresa'  => $empresa,
        ]);

        return $pdf->stream("compra_{$compra->id}.pdf");
    }
}
