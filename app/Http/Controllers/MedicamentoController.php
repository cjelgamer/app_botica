<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    // Listar medicamentos
    public function index()
    {
        $medicamentos = Medicamento::all();
        return response()->json($medicamentos);
    }

    // Mostrar un medicamento
    public function show($id)
    {
        $medicamento = Medicamento::find($id);
        if ($medicamento) {
            return response()->json($medicamento);
        }
        return response()->json(['message' => 'Medicamento no encontrado'], 404);
    }

    // Crear un nuevo medicamento
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Nombre' => 'required|string|max:255',
            'Descripcion' => 'nullable|string|max:255',
            'Precio' => 'required|numeric|min:0',
            'Stock' => 'required|integer|min:0',
            'Tipo' => 'nullable|string|max:100',
        ]);

        $medicamento = Medicamento::create($validatedData);
        return response()->json($medicamento, 201);
    }

    // Actualizar un medicamento
    public function update(Request $request, $id)
    {
        $medicamento = Medicamento::find($id);
        if (!$medicamento) {
            return response()->json(['message' => 'Medicamento no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'Nombre' => 'string|max:255',
            'Descripcion' => 'nullable|string|max:255',
            'Precio' => 'numeric|min:0',
            'Stock' => 'integer|min:0',
            'Tipo' => 'nullable|string|max:100',
        ]);

        $medicamento->update($validatedData);
        return response()->json($medicamento);
    }

    // Eliminar un medicamento
    public function destroy($id)
    {
        $medicamento = Medicamento::find($id);
        if (!$medicamento) {
            return response()->json(['message' => 'Medicamento no encontrado'], 404);
        }

        $medicamento->delete();
        return response()->json(['message' => 'Medicamento eliminado']);
    }


    // Método para obtener medicamentos con bajo stock
// Método para obtener medicamentos con bajo stock
public function medicamentoBajo()
{
    // Obtén los medicamentos cuyo stock esté entre 1 y 9
    $medicamentosBajos = Medicamento::whereBetween('Stock', [1, 9])->get();

    return response()->json($medicamentosBajos);
}


}
