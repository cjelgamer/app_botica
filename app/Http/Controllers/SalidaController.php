<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalidaController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
    
            $vendedor = Auth::guard('vendedor')->user();
            if (!$vendedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el vendedor en sesión'
                ], 401);
            }
    
            $request->validate([
                'Cliente_ID' => 'required|exists:Cliente,ID',
                'Monto_Total' => 'required|numeric|min:0',
                'Tipo_de_Pago' => 'required|string'
            ]);
    
            $salida = Salida::create([
                'Vendedor_ID' => $vendedor->ID,
                'Cliente_ID' => $request->Cliente_ID,
                'Monto_Total' => $request->Monto_Total,
                'Tipo_de_Pago' => $request->Tipo_de_Pago,
                'Fecha_Venta' => now()->format('Y-m-d')
            ]);
    
            DB::commit();
    
            // Recuperar la salida recién creada para asegurarnos de tener el ID
            $salida = Salida::find($salida->ID);
    
            return response()->json([
                'success' => true,
                'salida' => [
                    'ID' => $salida->ID,
                    'Vendedor_ID' => $salida->Vendedor_ID,
                    'Cliente_ID' => $salida->Cliente_ID,
                    'Monto_Total' => $salida->Monto_Total,
                    'Tipo_de_Pago' => $salida->Tipo_de_Pago,
                    'Fecha_Venta' => $salida->Fecha_Venta
                ],
                'message' => 'Venta registrada exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);
        }
    }
}