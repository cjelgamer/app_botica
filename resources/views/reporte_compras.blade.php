<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Compras</title>
    <style>
        /* Estilos CSS para el reporte */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Reporte de Compras</h1>
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
            @foreach ($entradasMedicamentos as $entrada)
                <tr>
                    <td>{{ $entrada->Medicamento }}</td>
                    <td>{{ $entrada->Laboratorio }}</td>
                    <td>{{ $entrada->Fecha_Entrega }}</td>
                    <td>{{ $entrada->Cantidad }}</td>
                    <td>${{ $entrada->Precio }}</td>
                    <td>${{ $entrada->Total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>