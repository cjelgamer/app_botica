<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use Illuminate\Http\Request;

class LaboratorioController extends Controller
{
    // Mostrar laboratorios
    public function index()
    {
        try {
            // Obtener todos los laboratorios
            $laboratorio = Laboratorio::all();
            return response()->json($laboratorio);
        } catch (\Exception $e) {
            // En caso de error, enviar mensaje de error
            return response()->json(['error' => 'Error al cargar los Laboratorios', 'message' => $e->getMessage()], 500);
        }
    }

    // Mostrar un laboratoriosespecífico
    public function show($id)
    {
        try {
            $laboratorio = Laboratorio::findOrFail($id);
            return response()->json($laboratorio);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Laboratorio no encontrado', 'message' => $e->getMessage()], 404);
        }
    }

    // Crear un nuevo laboratorios
    public function store(Request $request)
    {
        try {
            // Validación de los datos recibidos
            $validated = $request->validate([
                'Nombre' => 'required|string',
                'Telefono' => 'required|string',
                'Estado' => 'required|string',
                'Direccion' => 'required|string',
                'RUC' => 'required|string',
                //'Contraseña' => 'required|string', 
            ]);

            //*/ Crear el nuevo laboratorio con la contraseña encriptada automáticamente
            $laboratorio = Laboratorio::create($validated);

            return response()->json($laboratorio, 201); // 201 Creado exitosamente/
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear laboratorio', 'message' => $e->getMessage()], 500);
        }
    }

    // Actualizar los datos de un laboratorio
    public function update(Request $request, $id)
    {
        try {
            // Validación de los datos recibidos sin contraseña
            $validated = $request->validate([
                'Nombre' => 'required|string',
                'Telefono' => 'required|string',
                'Estado' => 'required|string',
                'Direccion' => 'required|string',
                'RUC' => 'required|string',
            ]);
    
            // Buscar el laboratorio por ID
            $laboratorio = Laboratorio::findOrFail($id);
    
            // Actualizar los datos validados
            $laboratorio->update($validated);
    
            return response()->json($laboratorio);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar laboratorio', 'message' => $e->getMessage()], 500);
        }
    }

    // Eliminar un laboratorio
    public function destroy($id)
    {
        try {
            // Buscar el laboratorio por ID
            $laboratorio = Laboratorio::findOrFail($id);

            // Eliminar el laboratorio
            $laboratorio ->delete();

            return response()->json(['message' => 'Laboratorio eliminado']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar laboratorio', 'message' => $e->getMessage()], 500);
        }
    }
}
