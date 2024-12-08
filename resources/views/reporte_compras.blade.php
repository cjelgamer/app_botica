<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Compras - Botica "Mi Salud"</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .fecha {
            margin-bottom: 20px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-general {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            font-size: 1.1em;
            padding: 10px;
            border-top: 2px solid #333;
        }
        .periodo-reporte {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
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
        <p>Botica "Mi Salud"</p>
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
                <th>Medicamento</th>
                <th>Laboratorio</th>
                <th>Fecha de Entrega</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalGeneral = 0;
                $totalMedicamentos = 0;
            @endphp
            @foreach ($entradasMedicamentos as $entrada)
                @php 
                    $totalGeneral += $entrada->Total;
                    $totalMedicamentos += $entrada->Cantidad;
                @endphp
                <tr>
                    <td>{{ $entrada->Medicamento }}</td>
                    <td>{{ $entrada->Laboratorio }}</td>
                    <td>{{ \Carbon\Carbon::parse($entrada->Fecha_Entrega)->format('d/m/Y') }}</td>
                    <td style="text-align: center;">{{ $entrada->Cantidad }}</td>
                    <td style="text-align: right;">S/. {{ number_format($entrada->Precio, 2) }}</td>
                    <td style="text-align: right;">S/. {{ number_format($entrada->Total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Totales:</strong></td>
                <td style="text-align: center;"><strong>{{ $totalMedicamentos }}</strong></td>
                <td colspan="2" style="text-align: right;"><strong>S/. {{ number_format($totalGeneral, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="total-general">
        Total General: S/. {{ number_format($totalGeneral, 2) }}
    </div>

    <div style="text-align: center; margin-top: 30px; font-size: 0.8em; color: #666;">
        © {{ date('Y') }} Botica "Mi Salud" - Todos los derechos reservados
    </div>
</body>
</html>