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
            date_default_timezone_set('America/Lima');
            $fechaActual = date('Y-m-d');
            $vendedor = Auth::guard('vendedor')->user();
            
            Log::info('Iniciando consulta de caja:', [
                'vendedor_id' => $vendedor ? $vendedor->ID : null,
                'fecha' => $fechaActual
            ]);
    
            if (!$vendedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el vendedor en sesión'
                ], 401);
            }
    
            // Obtener solo el registro del día actual
            $saldoActual = Caja::whereDate('Fecha', $fechaActual)
                             ->where('Vendedor_ID', $vendedor->ID)
                             ->orderBy('ID', 'desc')
                             ->first();
    
            // Si no hay registro del día actual, devolver 0
            if (!$saldoActual) {
                return response()->json([
                    'success' => true,
                    'saldo_final' => 0,
                    'vendedor_id' => $vendedor->ID,
                    'fecha_saldo' => $fechaActual
                ]);
            }
    
            // Si hay registro del día actual, retornar ese saldo
            return response()->json([
                'success' => true,
                'saldo_final' => $saldoActual->Saldo_Final,
                'vendedor_id' => $vendedor->ID,
                'fecha_saldo' => $saldoActual->Fecha
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