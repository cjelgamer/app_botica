<?php

namespace App\Http\Controllers;

use App\Models\VendedorSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendedorController extends Controller
{
    // Mostrar todos los vendedores
    public function index()
    {
        try {
            // Obtener todos los vendedores
            $vendedores = VendedorSimple::all();
            return response()->json($vendedores);
        } catch (\Exception $e) {
            // En caso de error, enviar mensaje de error
            return response()->json(['error' => 'Error al cargar los vendedores', 'message' => $e->getMessage()], 500);
        }
    }

    // Mostrar un vendedor específico
    public function show($id)
    {
        try {
            $vendedor = VendedorSimple::findOrFail($id);
            return response()->json($vendedor);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Vendedor no encontrado', 'message' => $e->getMessage()], 404);
        }
    }

    // Crear un nuevo vendedor
    public function store(Request $request)
    {
        try {
            // Validación de los datos recibidos
            $validated = $request->validate([
                'Nombre' => 'required|string',
                'Telefono' => 'required|string',
                'Estado' => 'required|string',
                'Rol' => 'required|string',
                'Contraseña' => 'required|string', // Validar que la contraseña sea obligatoria y mínima de 6 caracteres
            ]);

            // Crear el nuevo vendedor con la contraseña encriptada automáticamente
            $vendedor = VendedorSimple::create($validated);

            return response()->json($vendedor, 201); // 201 Creado exitosamente
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear vendedor', 'message' => $e->getMessage()], 500);
        }
    }

    // Actualizar los datos de un vendedor
    public function update(Request $request, $id)
    {
        try {
            // Validación de los datos recibidos sin contraseña
            $validated = $request->validate([
                'Nombre' => 'required|string',
                'Telefono' => 'required|string',
                'Estado' => 'required|string',
                'Rol' => 'required|string',
            ]);
    
            // Buscar el vendedor por ID
            $vendedor = VendedorSimple::findOrFail($id);
    
            // Actualizar los datos validados
            $vendedor->update($validated);
    
            return response()->json($vendedor);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar vendedor', 'message' => $e->getMessage()], 500);
        }
    }
    

    public function updatePassword(Request $request, $id)
    {
        try {
            // Validación de la contraseña
            $request->validate([
                'Contraseña' => 'required|string'
            ]);
    
            // Encontrar el vendedor
            $vendedor = VendedorSimple::findOrFail($id);
    
            // Actualizar la contraseña usando el mutador en el modelo
            $vendedor->Contraseña = $request->Contraseña;
            $vendedor->save();
    
            return response()->json(['message' => 'Contraseña actualizada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la contraseña', 'message' => $e->getMessage()], 500);
        }
    }
    

    public function updateEstado(Request $request, $id)
{
    try {
        // Validar el estado proporcionado
        $request->validate([
            'Estado' => 'required|string|in:Activo,Inactivo'
        ]);

        // Buscar el vendedor por ID
        $vendedor = VendedorSimple::findOrFail($id);

        // Actualizar el estado del vendedor
        $vendedor->Estado = $request->Estado;
        $vendedor->save();

        return response()->json(['message' => 'Estado actualizado correctamente', 'Estado' => $vendedor->Estado]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al actualizar el estado', 'message' => $e->getMessage()], 500);
    }
}

    // Eliminar un vendedor
    public function destroy($id)
    {
        try {
            // Buscar el vendedor por ID
            $vendedor = VendedorSimple::findOrFail($id);

            // Eliminar el vendedor
            $vendedor->delete();

            return response()->json(['message' => 'Vendedor eliminado']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar vendedor', 'message' => $e->getMessage()], 500);
        }
    }
}



