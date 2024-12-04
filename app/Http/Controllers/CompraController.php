<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\DetalleEntrada;

class CompraController extends Controller
{
    // Guardar Entrada
    public function guardarEntrada(Request $request)
    {
        $validatedData = $request->validate([
            'fecha_entrega' => 'required|date',
            'total' => 'required|numeric|min:0',
            'laboratorio_id' => 'required|exists:Laboratorio,ID',
        ]);
    
        \Log::info("Datos validados:", $validatedData); // Registra los datos validados
    
        try {
            $entrada = Entrada::create([
                'Fecha_Entrega' => $validatedData['fecha_entrega'],
                'Total' => $validatedData['total'],
                'Laboratorio_ID' => $validatedData['laboratorio_id'],
            ]);
    
            return response()->json([
                'message' => 'Entrada guardada exitosamente.',
                'id' => $entrada->ID,
            ], 201);
        } catch (\Exception $e) {
            \Log::error("Error al guardar la entrada:", ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'No se pudo guardar la entrada.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    

    // Agregar Detalle de Entrada
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
    

    // Listar todas las compras con sus detalles
    public function listarCompras()
    {
        try {
            $compras = Entrada::with(['detalles.medicamento', 'laboratorio'])->get();

            return response()->json([
                'message' => 'Lista de compras recuperada con éxito.',
                'data' => $compras,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudieron recuperar las compras.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


}
