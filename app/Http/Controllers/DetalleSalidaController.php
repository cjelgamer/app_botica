<?php

namespace App\Http\Controllers;

use App\Models\DetalleSalida;
use Illuminate\Http\Request;

class DetalleSalidaController extends Controller
{
    // Crear los detalles de la venta
    public function store(Request $request)
    {
        // Validar los datos del detalle
        $validated = $request->validate([
            'salida_id' => 'required|exists:salidas,id', // ID de la venta
            'medicamento_id' => 'required|exists:medicamentos,id', // ID del medicamento
            'cantidad' => 'required|numeric|min:1', // Cantidad del medicamento
            'precio_unitario' => 'required|numeric|min:0', // Precio unitario
            'precio_total' => 'required|numeric|min:0', // Precio total (cantidad * precio unitario)
        ]);

        // Crear el detalle de la venta (medicamento) en la base de datos
        $detalle = DetalleSalida::create([
            'salida_id' => $validated['salida_id'],
            'medicamento_id' => $validated['medicamento_id'],
            'cantidad' => $validated['cantidad'],
            'precio_unitario' => $validated['precio_unitario'],
            'precio_total' => $validated['precio_total'],
        ]);

        return response()->json([
            'message' => 'Detalle de la venta creado exitosamente',
            'detalle' => $detalle,
        ]);
    }
}

