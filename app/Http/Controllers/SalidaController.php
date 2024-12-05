<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use Illuminate\Http\Request;

class SalidaController extends Controller
{
    // Crear una nueva venta (Salida)
    public function store(Request $request)
    {
        // Validar los datos del formulario de la venta
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',  // ID del cliente
            'vendedor_id' => 'required|exists:users,id',    // ID del vendedor
            'monto_total' => 'required|numeric|min:0',      // Monto total de la venta
            'tipo_pago' => 'required|string|max:50',         // Tipo de pago
            'fecha_venta' => 'required|date',                // Fecha de la venta
        ]);

        // Crear la salida (venta) en la base de datos
        $salida = Salida::create([
            'cliente_id' => $validated['cliente_id'],
            'vendedor_id' => $validated['vendedor_id'],
            'monto_total' => $validated['monto_total'],
            'tipo_pago' => $validated['tipo_pago'],
            'fecha_venta' => $validated['fecha_venta'],
        ]);

        return response()->json([
            'message' => 'Venta registrada exitosamente',
            'salida' => $salida,
        ]);
    }

}
