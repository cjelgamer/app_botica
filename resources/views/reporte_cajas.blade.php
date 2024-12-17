<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cajas</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .fecha {
            text-align: right;
            margin-bottom: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        .total { 
            font-weight: bold; 
            margin-top: 20px; 
            text-align: right;
            padding: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Cajas</h1>
        <p>Fecha: {{ $fecha }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vendedor</th>
                <th style="text-align: right;">Saldo Final</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cajas as $caja)
            <tr>
                <td>{{ $caja->Vendedor }}</td>
                <td style="text-align: right;">S/. {{ number_format($caja->Saldo_Final, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total General: S/. {{ number_format(collect($cajas)->sum('Saldo_Final'), 2) }}
    </div>

    <div class="footer">
        © {{ date('Y') }} Sistema de Farmacia - Reporte generado el {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>