<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
                'Fecha_Venta' => Carbon::now('America/Lima')->format('Y-m-d')  // Ajustamos a la zona horaria correcta
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

    public function getHistorialVentas(Request $request)
    {
        try {
            $vendedor = Auth::guard('vendedor')->user();
            if (!$vendedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 401);
            }
    
            $ventas = Salida::select('ID', 'Fecha_Venta', 'Monto_Total', 'Tipo_de_Pago')
                ->where('Vendedor_ID', $vendedor->ID)
                ->orderBy('Fecha_Venta', 'desc')
                ->get()
                ->map(function ($venta) {
                    return [
                        'id' => $venta->ID,
                        'fecha' => date('Y-m-d', strtotime($venta->Fecha_Venta)), // Formateamos la fecha
                        'monto_total' => floatval($venta->Monto_Total),
                        'tipo_pago' => $venta->Tipo_de_Pago
                    ];
                });
    
            return response()->json([
                'success' => true,
                'ventas' => $ventas
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Error en historial:', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}