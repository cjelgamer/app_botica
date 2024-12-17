<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Compras - Botica "SISFARMA"</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #ffffff;
            color: #2e2e2e;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #4caf50;
            padding-bottom: 8px;
        }
        .header h1 {
            color: #ffffff;
            background-color: #2d6a4f;
            padding: 6px;
            border-radius: 3px;
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 4px 0;
            font-size: 12px;
        }
        .fecha {
            margin-bottom: 12px;
            color: #2e2e2e;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 12px;
            background-color: #ffffff;
        }
        th, td {
            border: 1px solid #2d6a4f;
            padding: 2px 4px;
            text-align: left;
            font-size: 9px;
            line-height: 1.1;
        }
        th {
            background-color: #2d6a4f;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-general {
            margin-top: 12px;
            text-align: right;
            font-weight: bold;
            font-size: 10px;
            padding: 6px;
            border-top: 2px solid #2d6a4f;
            color: #2e2e2e;
        }
        .periodo-reporte {
            text-align: center;
            color: #2e2e2e;
            margin-bottom: 12px;
            font-style: italic;
            font-size: 9px;
        }
        tfoot td {
            font-size: 9px;
            font-weight: bold;
            padding: 2px 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="/images/logo_c.png" alt="Sanar y sanar hasta morir" class="logo">
        <h1>
            @switch($tipo)
                @case('semana')
                    Reporte de Compras - Últimos 7 días
                    @break
                @case('mes')
                    Reporte de Compras - Último Mes
                    @break
                @default
                    Reporte de Compras Completo
            @endswitch
        </h1>
        <p>Botica "SISFARMA"</p>
    </div>

    <div class="fecha">
        Fecha de generación: {{ $fecha }}
    </div>

    <div class="periodo-reporte">
        @switch($tipo)
            @case('semana')
                Periodo: Últimos 7 días
                @break
            @case('mes')
                Periodo: Último mes
                @break
            @default
                Periodo: Histórico completo
        @endswitch
    </div>

    <table>
        <thead>
            <tr>
                <th>Medicamentos</th>
                <th>Laboratorio</th>
                <th>Fecha de Entrega</th>
                <th>Detalle de Cantidades</th>
                <th>Cantidad Total</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalGeneral = 0;
                $totalCantidades = 0;
            @endphp
            @foreach ($entradasMedicamentos as $entrada)
                @php 
                    $totalGeneral += $entrada->Total;
                    $totalCantidades += $entrada->Cantidad_Total;
                @endphp
                <tr>
                    <td>{{ $entrada->Medicamentos }}</td>
                    <td>{{ $entrada->Laboratorio }}</td>
                    <td>{{ \Carbon\Carbon::parse($entrada->Fecha_Entrega)->format('d/m/Y') }}</td>
                    <td>{{ $entrada->Detalle_Cantidades }}</td>
                    <td style="text-align: center;">{{ $entrada->Cantidad_Total }}</td>
                    <td style="text-align: right;">S/. {{ number_format($entrada->Total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Totales:</strong></td>
                <td style="text-align: center;"><strong>{{ $totalCantidades }}</strong></td>
                <td style="text-align: right;"><strong>S/. {{ number_format($totalGeneral, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="total-general">
        Total General: S/. {{ number_format($totalGeneral, 2) }}
    </div>

    <div style="text-align: center; margin-top: 12px; font-size: 8px; color: #2e2e2e;">
        © {{ date('Y') }} Botica "SISFARMA" - Todos los derechos reservados
    </div>
</body>
</html>