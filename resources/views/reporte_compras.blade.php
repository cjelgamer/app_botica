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
        }
        .header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #4caf50;
        padding-bottom: 10px;
        }
        .header h1 {
            color: #ffffff;
            background-color: #2d6a4f; /* Fondo verde */
            padding: 10px; /* Espaciado interno para el cuadro */
            border-radius: 3px; /* Bordes redondeados */
            margin: 0; /* Elimina el margen adicional */
        }
        .fecha {
            margin-bottom: 20px;
            color: #2e2e2e;
            font-size: 0.9em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        th, td {
            border: 1px solid #2d6a4f;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #2d6a4f;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-general {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            font-size: 1em;
            padding: 10px;
            border-top: 2px solid #2d6a4f;
            color: #2e2e2e;
        }
        .periodo-reporte {
            text-align: center;
            color: #2e2e2e;
            margin-bottom: 20px;
            font-style: italic;
            font-size: 1em;
        }
        a {
            color: #2d6a4f;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
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
                <th>Medicamento</th>
                <th>Laboratorio</th>
                <th>Fecha de Entrega</th>
                <th>Cantidad</th>
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
                    <td style="text-align: right;">S/. {{ number_format($entrada->Total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Totales:</strong></td>
                <td style="text-align: center;"><strong>{{ $totalMedicamentos }}</strong></td>
                <td style="text-align: right;"><strong>S/. {{ number_format($totalGeneral, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="total-general">
        Total General: S/. {{ number_format($totalGeneral, 2) }}
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 0.8em; color: #2e2e2e;">
        © {{ date('Y') }} Botica "SISFARMA" - Todos los derechos reservados
    </div>
</body>
</html>
