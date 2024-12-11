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
            'salida_id' => 'required|exists:salida,id',//id d ela venta 
            'medicamento_id' => 'required|exists:medicamentos,id', // ID del medicamento
            'cantidad' => 'required|numeric|min:1', // Cantidad del medicamento
        ]);

        // Crear el detalle de la venta (medicamento) en la base de datos
        $detalle = DetalleSalida::create([
            'salida_id' => $validated['salida_id'],
            'medicamento_id' => $validated['medicamento_id'],
            'cantidad' => $validated['cantidad'],
        ]);


        return response()->json([
            'message' => 'Detalle de la venta creado exitosamente',
            'detalle' => $detalle,
        ]);
    }

}

