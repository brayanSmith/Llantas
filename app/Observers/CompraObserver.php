<?php

namespace App\Observers;

use App\Models\Compra;

class CompraObserver
{
    /**
     * Handle the Compra "created" event.
     */
    public function created(Compra $compra): void
    {
        $this->syncEstadoPago($compra);
    }

    /**
     * Handle the Compra "updated" event.
     */
    public function updated(Compra $compra): void
    {
        $this->syncEstadoPago($compra);
    }

    /**
     * Sincroniza estado_pago según total a pagar (SALDADO si 0, EN_CARTERA si > 0)
     */
    private function syncEstadoPago(Compra $compra): void
    {
        $totalAPagar = $this->computeTotalAPagar($compra);

        $nuevoEstadoPago = (round($totalAPagar, 4) <= 0.0001) ? 'SALDADO' : 'EN_CARTERA';

        if (($compra->estado_pago ?? null) !== $nuevoEstadoPago) {
            // updateQuietly evita disparar observers recursivamente
            $compra->updateQuietly(['estado_pago' => $nuevoEstadoPago]);
        }
    }

    /**
     * Calcula un total a pagar fiable intentando usar campo total_a_pagar
     * o calculándolo desde subtotal - abonos - descuento.
     */
    private function computeTotalAPagar(Compra $compra): float
    {
        // Si ya existe el campo total_a_pagar en el modelo, usarlo
        if (isset($compra->total_a_pagar)) {
            return (float) $compra->total_a_pagar;
        }

        $subtotal = (float) ($compra->subtotal ?? 0);
        $descuento = (float) ($compra->descuento ?? 0);
        $abonosTotal = 0.0;

        // posibles nombres de relaciones de abonos y posibles nombres de columna de monto
        $relaciones = ['abonos', 'abonoCompra', 'abonosCompra', 'abonos_compra'];
        $montoCols = ['monto', 'monto_abono_compra', 'monto_abono', 'amount', 'valor'];

        foreach ($relaciones as $rel) {
            if (method_exists($compra, $rel)) {
                try {
                    $rows = $compra->{$rel}()->get();
                    foreach ($rows as $r) {
                        foreach ($montoCols as $col) {
                            if (isset($r->{$col})) {
                                $abonosTotal += (float) $r->{$col};
                                break;
                            }
                        }
                    }
                    // si encontramos filas, no probamos otras relaciones
                    if ($rows->isNotEmpty()) {
                        break;
                    }
                } catch (\Throwable $e) {
                    // ignorar y probar siguiente relación
                }
            }
        }

        $total = $subtotal - $abonosTotal - $descuento;
        return $total < 0 ? 0.0 : (float) $total;
    }


}