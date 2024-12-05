<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleEntrada;

class DetalleEntradaController extends Controller
{
    public function agregarDetalle(Request $request)
    {
        $validatedData = $request->validate([
            'entrada_id' => 'required|exists:Entrada,ID',
            'medicamento_id' => 'required|exists:Medicamento,ID',
            'cantidad' => 'required|integer|min:1',
        ]);

        try {
            $detalle = DetalleEntrada::create([
                'Entrada_ID' => $validatedData['entrada_id'],
                'Medicamento_ID' => $validatedData['medicamento_id'],
                'Cantidad' => $validatedData['cantidad'],
            ]);

            return response()->json([
                'message' => 'Detalle guardado exitosamente.',
                'detalle' => $detalle,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo guardar el detalle.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}
