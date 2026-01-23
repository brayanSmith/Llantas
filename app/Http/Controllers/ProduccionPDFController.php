<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produccion;
use Barryvdh\DomPDF\Facade\Pdf;

class ProduccionPDFController extends Controller
{
    //
    public function download($id)
    {
        $produccion = Produccion::with(['detallesProduccionSalidas', 'detallesProduccionEntradas'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.produccion', [
            'produccion' => $produccion,
            'detalles'   => $produccion->detallesProduccionSalidas,
        ]);

        return $pdf->download("produccion_{$produccion->id}.pdf");
    }

    public function stream($id)
        {
            $produccion = Produccion::with(['detallesProduccionSalidas', 'detallesProduccionEntradas'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.produccion', [
            'produccion' => $produccion,
            'detalles'   => $produccion->detallesProduccionSalidas,
        ]);

        return $pdf->stream("produccion_{$produccion->id}.pdf");
    }
}
