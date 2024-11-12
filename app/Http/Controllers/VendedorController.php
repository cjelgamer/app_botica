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
            // Validación de los datos recibidos
            $validated = $request->validate([
                'Nombre' => 'required|string',
                'Telefono' => 'required|string',
                'Estado' => 'required|string',
                'Rol' => 'required|string',
                'Contraseña' => 'required|string', // Contraseña obligatoria para la actualización
            ]);

            // Buscar el vendedor por ID
            $vendedor = VendedorSimple::findOrFail($id);

            // Actualizar la contraseña si se proporciona
            if ($request->filled('Contraseña')) {
                $vendedor->Contraseña = $validated['Contraseña']; // El mutador setContraseñaAttribute se encargará de encriptar
            }

            // Actualizar los datos del vendedor
            $vendedor->update($validated);

            return response()->json($vendedor);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar vendedor', 'message' => $e->getMessage()], 500);
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
