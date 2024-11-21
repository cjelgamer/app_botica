<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;

class EntradaController extends Controller
{
    public function guardarEntrada(Request $request)
    {
        \Log::info('Datos recibidos en el servidor:', $request->all());
    
        $validatedData = $request->validate([
            'fecha_entrega' => 'required|date',
            'total' => 'required|numeric|min:0',
            'laboratorio_id' => 'required|exists:Laboratorio,ID',
        ]);
    
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
            \Log::error('Error al guardar la entrada:', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'No se pudo guardar la entrada.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    

}
