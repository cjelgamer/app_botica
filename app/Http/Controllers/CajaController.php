<?php
namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function getSaldoActual(Request $request)
    {
        try {
            // Obtener el vendedor autenticado
            $vendedor = Auth::guard('vendedor')->user();
            if (!$vendedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el vendedor en sesión'
                ], 401);
            }
            
            $vendedorId = $vendedor->id; // Usar 'id' en minúsculas

            // Consultar el saldo actual basado en el vendedor autenticado
            $saldoActual = Caja::whereDate('Fecha', date('Y-m-d'))
                               ->where('Vendedor_ID', $vendedor->ID)
                               ->latest('id') // Obtener el registro más reciente
                               ->first();

            return response()->json([
                'success' => true,
                'saldo_final' => $saldoActual ? $saldoActual->Saldo_Final : 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener saldo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
