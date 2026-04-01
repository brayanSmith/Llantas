<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $pedido->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background: #fff;
            width: 72mm;          /* Ajustar: 58mm para rollo de 58mm */
            margin: 0 auto;
            padding: 4mm 2mm;
        }

        /* ── Utilidades ── */
        .center   { text-align: center; }
        .bold     { font-weight: bold; }
        .right    { text-align: right; }
        .small    { font-size: 10px; }

        /* ── Separadores ── */
        .sep-dash {
            border: none;
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        .sep-solid {
            border: none;
            border-top: 1px solid #000;
            margin: 4px 0;
        }

        /* ── Cabecera empresa ── */
        .empresa-nombre {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .logo-wrap {
            text-align: center;
            margin-bottom: 4px;
        }
        .logo-wrap img {
            max-width: 90px;
            max-height: 60px;
            object-fit: contain;
        }

        /* ── Fila clave-valor ── */
        .row {
            display: flex;
            justify-content: space-between;
            margin: 1px 0;
        }
        .row span:first-child { font-weight: bold; }

        /* ── Tabla de productos ── */
        .tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin: 3px 0;
        }
        .tbl th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 2px 1px;
            font-weight: bold;
        }
        .tbl td {
            padding: 2px 1px;
            vertical-align: top;
        }
        .tbl td.r { text-align: right; }
        .tbl .nombre { max-width: 90px; word-break: break-word; }

        /* ── Totales ── */
        .totales {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            font-size: 11px;
        }
        .totales td { padding: 1px 0; }
        .totales td:last-child { text-align: right; }
        .total-final td {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 3px;
        }

        /* ── Saldo vencido destacado ── */
        .saldo-vencido {
            font-weight: bold;
            text-decoration: underline;
        }

        /* ── Pie ── */
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 6px;
        }

        /* ── Ocultar en pantalla lo que es solo impresión ── */
        @media screen {
            body { border: 1px dashed #ccc; }
        }

        @media print {
            body {
                width: 72mm;      /* Ajustar a tu rollo */
                margin: 0;
                padding: 3mm 1mm;
            }
            /* Forzar blanco/negro */
            * { color: #000 !important; background: #fff !important; }
        }
    </style>
</head>
<body>

    {{-- ─── LOGO ─── --}}
    @if ($empresa && $empresa->logo_empresa)
        @php
            $logoPath   = storage_path('app/public/' . $empresa->logo_empresa);
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoData   = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
            }
        @endphp
        @if ($logoBase64)
            <div class="logo-wrap">
                <img src="{{ $logoBase64 }}" alt="Logo">
            </div>
        @endif
    @endif

    {{-- ─── EMPRESA ─── --}}
    <div class="center" style="margin-bottom:3px;">
        <div class="empresa-nombre">{{ $empresa->nombre_empresa ?? 'DISTRIGUERRERO' }}</div>
        <div>Distribuidora de Ferreterías</div>
        <div>{{ $empresa->direccion_empresa ?? 'CALLE 7 NUMERO 5-63' }}</div>
        <div>Tel: {{ $empresa->telefono_empresa ?? '3105568244' }}</div>
        <div>NIT: {{ $empresa->nit_empresa ?? '1087644203-1' }}</div>
    </div>
    {{-- TURNO --}}

    @if ($pedido->turno)
        <div class="center" style="margin-bottom:6px;">
            <div style="font-size:18px; font-weight:bold; letter-spacing:2px; border-bottom:2px solid #000; display:inline-block; padding:2px 12px; margin-bottom:2px;">
                {{ $pedido->turno }}
            </div>
        </div>
    @endif

    <hr class="sep-dash">

    {{-- ─── REMISIÓN / VENCIMIENTO ─── --}}
    <div class="row">
        <span>REMISION N°:</span>
        <span>{{ $pedido->id }}</span>
    </div>

    <hr class="sep-dash">

    {{-- ─── FECHA / VENDEDOR / PAGO ─── --}}
    <div class="center">
        <div><span class="bold">Fecha:</span> {{ $pedido->fecha->format('d/m/Y H:i') }}</div>
        <div><span class="bold">Vendedor:</span> {{ $pedido->user->name ?? 'N/A' }}</div>
        <div><span class="bold">Pago:</span> {{ $pedido->puc->concatenar_subcuenta_concepto ?? 'N/A' }}</div>
    </div>

    <hr class="sep-dash">

    {{-- ─── CLIENTE ─── --}}
    <div><span class="bold">CLIENTE:</span> {{ $pedido->cliente->razon_social ?? 'N/A' }}</div>
    <div><span class="bold">NIT:</span> {{ $pedido->cliente->numero_documento ?? 'N/A' }}</div>
    <div><span class="bold">CIUDAD:</span> {{ $pedido->cliente->ciudad ?? 'N/A' }}</div>
    <div><span class="bold">DIR:</span> {{ $pedido->cliente->direccion ?? 'N/A' }}</div>
    <div><span class="bold">TEL:</span> {{ $pedido->cliente->telefono ?? 'N/A' }}</div>

    <hr class="sep-dash">


    {{-- ─── PRODUCTOS ─── --}}
    <div class="bold small" style="margin-bottom:2px;">PRODUCTOS FACTURADOS</div>

    <table class="tbl">
        <thead>
            <tr>
                <th style="width:40px;">ITEM</th>
                <th class="r" style="width:44px; text-align:right;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $detalle)
            <tr>
                <td>{{ $detalle->cantidad }} X {{ $detalle->producto->concatenar_codigo_nombre ?? '-' }}</td>
                <td class="r">${{ number_format($detalle->subtotal, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="sep-dash">

    {{-- ─── TOTALES ─── --}}
    <table class="totales">
        <tr>
            <td>Subtotal</td>
            <td>${{ number_format($pedido->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Descuento</td>
            <td>- ${{ number_format($pedido->descuento, 2) }}</td>
        </tr>
        <tr>
            <td>Flete</td>
            <td>${{ number_format($pedido->flete, 2) }}</td>
        </tr>
        <tr class="total-final">
            <td>TOTAL</td>
            <td>${{ number_format($pedido->total_a_pagar, 2) }}</td>
        </tr>
    </table>

    <hr class="sep-dash">

    {{-- ─── OBSERVACIONES ─── --}}
    @if ($pedido->primer_comentario || $pedido->segundo_comentario)
    <div class="small">
        @if ($pedido->primer_comentario)
            <div><span class="bold">OBS 1:</span> {{ $pedido->primer_comentario }}</div>
        @endif
        @if ($pedido->segundo_comentario)
            <div><span class="bold">OBS 2:</span> {{ $pedido->segundo_comentario }}</div>
        @endif
    </div>
    <hr class="sep-dash">
    @endif

    {{-- ─── PIE DE PÁGINA ─── --}}
    <div class="footer">
        <div class="bold">ABONOS A:</div>
        @if (!empty($cuentas_bancarias))
            @foreach ($cuentas_bancarias as $cuenta)
                <div>{{ $cuenta['cuenta'] ?? '' }}</div>
            @endforeach
        @else
            <div>No hay cuentas registradas.</div>
        @endif

        <hr class="sep-dash">
        <div>Cambios: 10 días calendario</div>
        <div>Faltantes: 5 días calendario</div>
        <div class="bold" style="margin-top:4px;">¡GRACIAS POR SU COMPRA!</div>
    </div>

</body>
</html>
