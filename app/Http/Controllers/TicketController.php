<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Detalle_Salida;
use App\Models\Cliente;
use Carbon\Carbon;
use PDF;

class TicketController extends Controller
{
    public function __construct()
    {
        // Establecer la zona horaria para todas las operaciones de Carbon
        Carbon::setLocale('es');
        date_default_timezone_set('America/Lima');
    }

    public function generarTicket($salidaId)
    {
        // Obtener datos de la venta con relaciones
        $venta = Salida::with([
            'cliente',
            'detalles.medicamento',
            'vendedor'
        ])->findOrFail($salidaId);
        
        // Formatear la fecha usando Carbon con zona horaria de Perú
        $fecha = $venta->created_at 
            ? Carbon::parse($venta->created_at)
                ->setTimezone('America/Lima')
                ->format('d/m/Y H:i:s')
            : Carbon::now('America/Lima')->format('d/m/Y H:i:s');
        
        // Configurar datos para el ticket
        $ticketData = [
            'empresa' => [
                'nombre' => 'SISFARMA',
                'ruc' => 'RUC: 111111111',
                'direccion' => 'Lima Central',
                'telefono' => 'TELF: 111-1111'
            ],
            'venta' => [
                'numero' => str_pad($venta->ID, 10, '0', STR_PAD_LEFT),
                'fecha' => $fecha,
                'cliente' => $venta->cliente ? $venta->cliente->Nombre . ' ' . $venta->cliente->Apellido_Pat : 'Cliente General',
                'dni' => $venta->cliente ? $venta->cliente->DNI : '-',
                'tipo_pago' => $venta->Tipo_de_Pago,
                'vendedor' => $venta->vendedor ? $venta->vendedor->Nombre : 'Sistema'
            ],
            'detalles' => $venta->detalles->map(function ($detalle) {
                return [
                    'cantidad' => $detalle->Cantidad,
                    'descripcion' => $detalle->medicamento->Nombre,
                    'precio' => $detalle->medicamento->Precio,
                    'subtotal' => $detalle->Cantidad * $detalle->medicamento->Precio
                ];
            }),
            'totales' => [
                'subtotal' => $venta->Monto_Total / 1.18,
                'igv' => $venta->Monto_Total - ($venta->Monto_Total / 1.18),
                'total' => $venta->Monto_Total
            ]
        ];

        // Generar PDF
        $pdf = PDF::loadView('venta', $ticketData);
        $pdf->setPaper([0, 0, 226.77, 500], 'portrait'); // Tamaño ticket 80mm

        return $pdf->stream('ticket-' . $salidaId . '.pdf');
    }
}