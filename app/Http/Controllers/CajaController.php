<?php
namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CajaController extends Controller
{
    public function getSaldoActual(Request $request)
    {
        try {
            $vendedor = Auth::guard('vendedor')->user();
            
            // Agregar log para debug
            Log::info('Vendedor en sesión:', [
                'vendedor_id' => $vendedor ? $vendedor->ID : null
            ]);

            if (!$vendedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el vendedor en sesión'
                ], 401);
            }

            // Usar ID mayúscula consistentemente
            $saldoActual = Caja::whereDate('Fecha', date('Y-m-d'))
                             ->where('Vendedor_ID', $vendedor->ID)
                             ->orderBy('ID', 'desc') // Usar ID en mayúscula
                             ->first();

            // Log para debug
            Log::info('Consulta de saldo:', [
                'fecha' => date('Y-m-d'),
                'vendedor_id' => $vendedor->ID,
                'saldo' => $saldoActual ? $saldoActual->Saldo_Final : 0
            ]);

            return response()->json([
                'success' => true,
                'saldo_final' => $saldoActual ? $saldoActual->Saldo_Final : 0,
                'vendedor_id' => $vendedor->ID // Agregar para debug
            ]);
        } catch (\Exception $e) {
            Log::error('Error en getSaldoActual:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener saldo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}