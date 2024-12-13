<!-- resources/views/venta.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket de Venta</title>
    <style>
        @page {
            margin: 5mm;
        }
        body {
            font-family: 'Courier', monospace;
            font-size: 9pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .contenedor {
            width: 70mm;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .empresa-nombre {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }
        .empresa-info {
            font-size: 9pt;
            line-height: 1.1;
        }
        .linea-punteada {
            border-top: 1px dotted #000;
            margin: 2mm 0;
        }
        .info-ticket {
            margin-bottom: 3mm;
        }
        .info-ticket table {
            width: 100%;
            font-size: 9pt;
        }
        .info-ticket td {
            padding: 0.5mm 0;
        }
        .tabla-productos {
            width: 100%;
            margin: 3mm 0;
            border-collapse: collapse;
        }
        .tabla-productos th {
            text-align: left;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-size: 9pt;
            padding: 1mm 0;
        }
        .tabla-productos td {
            padding: 1mm 0;
            font-size: 9pt;
        }
        .totales {
            margin: 3mm 0;
            text-align: right;
        }
        .totales table {
            width: 100%;
        }
        .totales td {
            padding: 0.5mm 0;
            font-size: 9pt;
        }
        .footer {
            text-align: center;
            margin-top: 5mm;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="header">
            <div class="empresa-nombre">SISFARMA</div>
            <div class="empresa-info">
                RUC: 111111111<br>
                Lima Central<br>
                TELF: 111-1111
            </div>
        </div>

        <div class="linea-punteada"></div>

        <div class="info-ticket">
            <table>
                <tr>
                    <td width="18%"><strong>TICKET:</strong></td>
                    <td width="42%">{{ str_pad($venta['numero'], 10, '0', STR_PAD_LEFT) }}</td>
                    <td width="18%"><strong>FECHA:</strong></td>
                    <td width="22%">{{ $venta['fecha'] }}</td>
                </tr>
                <tr>
                    <td><strong>CLIENTE:</strong></td>
                    <td>{{ $venta['cliente'] }}</td>
                    <td><strong>DNI:</strong></td>
                    <td>{{ $venta['dni'] }}</td>
                </tr>
                <tr>
                    <td><strong>VEND:</strong></td>
                    <td>{{ $venta['vendedor'] }}</td>
                    <td><strong>PAGO:</strong></td>
                    <td>{{ strtoupper($venta['tipo_pago']) }}</td>
                </tr>
            </table>
        </div>

        <div class="linea-punteada"></div>

        <table class="tabla-productos">
            <thead>
                <tr>
                    <th width="10%">CANT</th>
                    <th width="50%">DESCRIPCIÓN</th>
                    <th width="20%">P.UNIT</th>
                    <th width="20%">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalles as $detalle)
                <tr>
                    <td>{{ $detalle['cantidad'] }}</td>
                    <td>{{ $detalle['descripcion'] }}</td>
                    <td>{{ number_format($detalle['precio'], 2) }}</td>
                    <td>{{ number_format($detalle['subtotal'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="linea-punteada"></div>

        <div class="totales">
            <table>
                <tr>
                    <td width="60%"></td>
                    <td width="20%"><strong>SUBTOTAL:</strong></td>
                    <td width="20%">S/. {{ number_format($totales['subtotal'], 2) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><strong>IGV (18%):</strong></td>
                    <td>S/. {{ number_format($totales['igv'], 2) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><strong>TOTAL:</strong></td>
                    <td>S/. {{ number_format($totales['total'], 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="linea-punteada"></div>

        <div class="footer">
            Gracias por su compra<br>
            ¡Vuelva pronto!
        </div>
    </div>
</body>
</html>