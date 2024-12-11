<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'DNI' => 'required|string|max:20|unique:Cliente,DNI',
                'Nombre' => 'required|string|max:255',
                'Apellido_Pat' => 'required|string|max:255',
                'Apellido_Mat' => 'required|string|max:255',
            ]);

            $cliente = Cliente::create([
                'DNI' => $request->DNI,
                'Nombre' => $request->Nombre,
                'Apellido_Pat' => $request->Apellido_Pat,
                'Apellido_Mat' => $request->Apellido_Mat
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'cliente' => $cliente,
                'message' => 'Cliente registrado exitosamente'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function buscarPorDNI($dni)
    {
        try {
            $cliente = Cliente::where('DNI', $dni)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'cliente' => $cliente
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}