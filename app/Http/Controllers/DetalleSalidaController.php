<?php
namespace App\Http\Controllers;

use App\Models\DetalleSalida;
use Illuminate\Http\Request;

class DetalleSalidaController extends Controller
{
    // Crear los detalles de la venta
    public function store(Request $request)
    {
        try {
            // Validar los datos del detalle
            $validated = $request->validate([
                'salida_id' => 'required|exists:salida,id', // ID de la venta
                'medicamento_id' => 'required|exists:medicamentos,id', // ID del medicamento
                'cantidad' => 'required|numeric|min:1', // Cantidad del medicamento
            ]);

            // Crear el detalle de la venta (medicamento) en la base de datos
            $detalle = DetalleSalida::create([
                'Salida_ID' => $validated['salida_id'],
                'Medicamento_ID' => $validated['medicamento_id'],
                'Cantidad' => $validated['cantidad'],
            ]);

            // Retornar una respuesta exitosa con el detalle creado
            return response()->json([
                'message' => 'Detalle de la venta creado exitosamente.',
                'detalle' => $detalle,
            ], 201); // Código de estado 201 para creado exitosamente

        } catch (\Exception $e) {
            // Loguear el error
            \Log::error('Error al guardar el detalle de la salida:', ['exception' => $e->getMessage()]);

            // Retornar una respuesta de error
            return response()->json([
                'error' => 'No se pudo guardar el detalle de la salida.',
                'details' => $e->getMessage(),
            ], 500); // Código de estado 500 para error interno del servidor
        }
    }
}
