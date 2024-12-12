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
    
            \Log::info('Iniciando registro de cliente', $request->all());
    
            $request->validate([
                'DNI' => 'required|string|max:20|unique:Cliente,DNI',
                'Nombre' => 'required|string|max:255',
                'Apellido_Pat' => 'required|string|max:255',
                'Apellido_Mat' => 'required|string|max:255',
            ]);
    
            // Creamos el cliente y verificamos que se haya creado
            $cliente = Cliente::create([
                'DNI' => $request->DNI,
                'Nombre' => $request->Nombre,
                'Apellido_Pat' => $request->Apellido_Pat,
                'Apellido_Mat' => $request->Apellido_Mat
            ]);
    
            if (!$cliente) {
                throw new \Exception('No se pudo crear el cliente en la base de datos');
            }
    
            // Verificamos que el ID exista
            if (!$cliente->ID) {
                throw new \Exception('El cliente se creó pero no se generó un ID');
            }
    
            // Convertimos a array antes de enviarlo
            $clienteArray = [
                'ID' => $cliente->ID,
                'DNI' => $cliente->DNI,
                'Nombre' => $cliente->Nombre,
                'Apellido_Pat' => $cliente->Apellido_Pat,
                'Apellido_Mat' => $cliente->Apellido_Mat
            ];
    
            \Log::info('Cliente creado exitosamente', ['cliente' => $clienteArray]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'cliente' => $clienteArray,
                'message' => 'Cliente registrado exitosamente'
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Error de validación', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al registrar cliente', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
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