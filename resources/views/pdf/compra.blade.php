<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>COMPRA #{{ $compra->id }}</title>
    <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      margin: 30px;
      color: #000;
    }

    .header,
    .cliente-info {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .centered {
      text-align: center;
    }

    .bold {
      font-weight: bold;
    }

    .red {
      color: red;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .table th,
    .table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: left;
    }

    .table th {
      background-color: #f2f2f2;
    }

    .totals td {
      text-align: right;
    }

    .logo {
      width: 180px;
    }

    .section {
      margin-top: 20px;
    }

    .observacion {
      margin-top: 30px;
    }

    .separator {
      border-top: 2px dashed #333;
      margin: 15px 0;
    }
  </style>
</head>
<body>



  <!-- Logo centrado -->

  <div style="text-align: center; margin-bottom: 40px;">
    @if($empresa && $empresa->logo_empresa)
        @php
            $logoPath = storage_path('app/public/' . $empresa->logo_empresa);
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoData = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
            }
        @endphp
        @if($logoBase64)
            <img
                src="{{ $logoBase64 }}"
                alt="Logo Empresa"
                style="width: 120px; height: auto; max-height: 80px; object-fit: contain;"
            />
        @endif
    @endif
  </div>


  <!-- Información general centrada -->
<div style="text-align: center; margin-bottom: 10px;">
  <div class="bold" style="font-size: 24px;">{{ $empresa->nombre_empresa ?? 'DISTRIGUERRERO' }}</div>
  <div>Distribuidora de Ferreterías</div>
  <div>{{ $empresa->direccion_empresa ?? 'CALLE 7 NUMERO 5-63' }}</div>
  <div>{{ $empresa->telefono_empresa ?? '3105568244' }}</div>
  <div>{{ $empresa->nit_empresa ?? '1087644203-1' }}</div>


</div>

<!-- Línea con REMISIÓN a la izquierda y FECHA DE VENCIMIENTO a la derecha -->
<div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
  <div><strong>COMPRA N°:</strong> <span class="bold">{{ $compra->codigo }}</span></div>
  <div><strong>FECHA:</strong> <span> {{ $compra->fecha }}</span> </div>
</div>
<div class="separator"></div>

<div style="text-align: center; margin-top: 10px;">
    {{-- -<strong>Fecha Venta:</strong> {{ $compra->fecha->format('d/m/Y H:i') }} <br> --}}
    <strong>Forma de pago:</strong> {{ $compra->metodo_pago }}
  </div>

  <div class="section cliente-info">
    <div>
      <div><strong>PROVEEDOR:</strong> <span id="nombreCliente">{{ $compra->proveedor->nombre_proveedor ?? 'N/A' }}</span></div>
      <div><strong>NIT:</strong> <span id="nDocCliente">{{ $compra->proveedor->nit_proveedor ?? 'N/A' }}</span></div>
      <div><strong>CIUDAD:</strong> <span id="ciudadCliente">{{ $compra->proveedor->ciudad_proveedor ?? 'N/A' }}</span></div>
      <div><strong>DIRECCIÓN:</strong> <span id="direccionCliente">{{ $compra->proveedor->direccion_proveedor ?? 'N/A' }}</span></div>
      <div><strong>TELEFONO:</strong> <span id="telefonoCliente">{{ $compra->proveedor->telefono_proveedor ?? 'N/A' }}</span></div>
    </div>

    {{--<div style="align-self: flex-start;">
      <strong>SALDO VENCIDO:</strong> <span class="red" id="saldoVencido">{{ $compra->saldo_vencido ?? 'N/A' }}</span>
    </div>--}}
  </div>

  {{-- <div class="section">
    <strong>TIPO </strong> <span id="tipoCliente">{{ $compra->tipo_venta ?? 'N/A' }}</span>
  </div> --}}

  <h3>Productos Compra</h3>
   <!-- Tabla de productos -->
  <table class="table">
    <thead>
      <tr>
        <th>ITEM</th>
        <th>DESCRIPCIÓN</th>
        <th>CANTIDAD</th>
        <th>UNITARIO</th>
        <th>IVA</th>
        <th>SUBTOTAL</th>
      </tr>
    </thead>
    @foreach($detallesCompra as $detalle)
                <tr>
                    <td>
                        @if($detalle->tipo_item === 'PRODUCTO')
                            {{ $detalle->producto->concatenar_codigo_nombre ?? 'N/A' }}
                        @elseif($detalle->tipo_item === 'GASTO')
                            {{ $detalle->puc->concatenar_subcuenta_concepto ?? 'N/A' }}
                        @else
                            No definido
                        @endif
                    </td>
                    <td>{{ $detalle->descripcion_item ?? 'N/A' }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->iva, 2) }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
    </tbody>
  </table>

  <!-- Totales -->
  <table class="table totals" style="width: 60%; float: right; margin-top: 10px;">
    <tr>
      <td><strong>Sub Total</strong></td>
      <td><span>{{ number_format($compra->subtotal, 2) }}</span></td>
    </tr>
    <tr>
      <td><strong>Descuento</strong></td>
      <td><span>{{ number_format($compra->descuento, 2) }}</span></td>
    </tr>
    <tr>
      <td><strong>Total</strong></td>
      <td><strong id="total">{{ number_format($compra->total_a_pagar, 2) }}</strong></td>
    </tr>
  </table>

 <!-- Observaciones -->
  <div class="observacion">
    <div><strong>OBSERVACIÓN 1:</strong><span id="comentario1">{{ $compra->primer_comentario ?? 'N/A' }}</span></div>
    <div><strong>OBSERVACIÓN 2:</strong><span id="comentario2">{{ $compra->segundo_comentario ?? 'N/A' }}</span></div>
  </div>


</body>
</html>
