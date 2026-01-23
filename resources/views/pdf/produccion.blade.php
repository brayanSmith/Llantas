<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Producción #{{ $produccion->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 30px;
            color: #222;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .header-title {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #2c3e50;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 18px 0 8px 0;
            color: #34495e;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border: 1px solid #bbb;
            padding: 6px 8px;
            text-align: left;
        }
        .table th {
            background-color: #f7f7f7;
            color: #222;
        }
        .totals td {
            text-align: right;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title">Producción N° {{ $produccion->id }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Bodega:</td>
            <td>{{ $produccion->bodega->nombre_bodega }}</td>
            <td class="label">Cantidad:</td>
            <td>{{ $produccion->cantidad }}</td>
            <td class="label">Lote:</td>
            <td>{{ $produccion->lote }}</td>
        </tr>
        <tr>
            <td class="label">Fecha Producción:</td>
            <td>{{ $produccion->fecha_produccion }}</td>
            <td class="label">Fecha Caducidad:</td>
            <td>{{ $produccion->fecha_caducidad }}</td>
        </tr>
        <tr>
            <td class="label">Responsable:</td>
            <td>{{ $produccion->responsableLote->name }}</td>
            <td class="label">Responsable CC:</td>
            <td>{{ $produccion->responsableCC->name }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="label">Observaciones:</td>
            <td>{{ $produccion->observaciones }}</td>
        </tr>
    </table>

    <div class="section-title">Detalles de Producción</div>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad Producida</th>
                <th>Fecha de Producción</th>
                <th>Observaciones</th>
                <th>Lote</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produccion->detallesProduccionEntradas as $detalle)
            <tr>
                <td>{{ $detalle->producto->nombre_producto }}</td>
                <td>{{ $detalle->cantidad_producto }}</td>
                <td>{{ $detalle->fecha_produccion }}</td>
                <td>{{ $detalle->observaciones }}</td>
                <td>{{ $detalle->lote }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px;"></div>
    <div class="section-title">Control de Calidad</div>
    <table class="table" style="width: 60%; margin: 0 auto;">
        <thead>
            <tr>
                <th style="text-align:center;">Ph</th>
                <th style="text-align:center;">Viscosidad</th>
                <th style="text-align:center;">Homogeneidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align:center;">{{ $produccion->ph }}</td>
                <td style="text-align:center;">{{ $produccion->biscocidad }}</td>
                <td style="text-align:center;">{{ $produccion->homogeneidad }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
