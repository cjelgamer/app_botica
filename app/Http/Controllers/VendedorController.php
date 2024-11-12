<?php

namespace App\Http\Controllers;

use App\Models\VendedorSimple;
use Illuminate\Http\Request;

class VendedorController extends Controller
{
    // Mostrar todos los vendedores
    public function index()
    {
        $vendedores = VendedorSimple::all();
        return response()->json($vendedores);
    }

    // Mostrar un vendedor específico
    public function show($id)
    {
        $vendedor = VendedorSimple::findOrFail($id);
        return response()->json($vendedor);
    }

    // Crear un nuevo vendedor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nombre' => 'required|string',
            'Telefono' => 'required|string',
            'Estado' => 'required|string',
            'Rol' => 'required|string',
        ]);

        $vendedor = VendedorSimple::create($validated);
        return response()->json($vendedor, 201); // 201 creado
    }

    // Actualizar los datos de un vendedor
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Nombre' => 'required|string',
            'Telefono' => 'required|string',
            'Estado' => 'required|string',
            'Rol' => 'required|string',
        ]);

        $vendedor = VendedorSimple::findOrFail($id);
        $vendedor->update($validated);
        return response()->json($vendedor);
    }

    // Eliminar un vendedor
    public function destroy($id)
    {
        $vendedor = VendedorSimple::findOrFail($id);
        $vendedor->delete();
        return response()->json(['message' => 'Vendedor eliminado']);
    }
}
