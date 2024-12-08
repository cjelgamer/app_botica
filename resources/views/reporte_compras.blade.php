<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Compras</title>
    <style>
        /* Mantén los estilos que ya tienes y agrega estos */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .total-general {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Compras</h1>
        <p>Botica "Mi Salud"</p>
    </div>
    
    <div class="fecha">
        Fecha de generación: {{ $fecha }}
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
            @php $totalGeneral = 0; @endphp
            @foreach ($entradasMedicamentos as $entrada)
                @php $totalGeneral += $entrada->Total; @endphp
                <tr>
                    <td>{{ $entrada->Medicamento }}</td>
                    <td>{{ $entrada->Laboratorio }}</td>
                    <td>{{ \Carbon\Carbon::parse($entrada->Fecha_Entrega)->format('d/m/Y') }}</td>
                    <td>{{ $entrada->Cantidad }}</td>
                    <td>S/. {{ number_format($entrada->Precio, 2) }}</td>
                    <td>S/. {{ number_format($entrada->Total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-general">
        Total General: S/. {{ number_format($totalGeneral, 2) }}
    </div>
</body>
</html>