<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use Illuminate\Http\Request;

class SalidaController extends Controller
{
    // Listar todas las salidas
    public function index()
    {
        return Salida::with('detalles')->get(); // Incluye los detalles de la salida
    }

    // Ver detalles de una salida específica
    public function show($id)
    {
        $salida = Salida::with('detalles.medicamento')->find($id);
        if (!$salida) {
            return response()->json(['error' => 'Salida no encontrada'], 404);
        }
        return $salida;
    }

}
