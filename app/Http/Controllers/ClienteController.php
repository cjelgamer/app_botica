<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente; // Importar el modelo Cliente

class ClienteController extends Controller
{
    public function guardarCliente(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'dni' => 'required|string|max:20',
            'nombre' => 'required|string|max:255',
            'apellido_pat' => 'required|string|max:255',
            'apellido_mat' => 'nullable|string|max:255', // Es opcional
        ]);

        // Crear o actualizar el cliente en la base de datos
        $cliente = Cliente::updateOrCreate(
            ['DNI' => $request->dni], // Buscar por DNI
            [
                'Nombre' => $request->nombre,
                'Apellido_Pat' => $request->apellido_pat,
                'Apellido_Mat' => $request->apellido_mat ?? null,
            ]
        );

        return response()->json([
            'message' => 'Cliente guardado exitosamente.',
            'cliente' => $cliente
        ]);
    }
}
