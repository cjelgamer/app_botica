<?php

namespace App\Http\Controllers;

use App\Models\DetalleSalida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleSalidaController extends Controller
{
    public function storeMasivo(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'salida_id' => 'required|exists:Salida,ID',
                'detalles' => 'required|array',
                'detalles.*.medicamento_id' => 'required|exists:Medicamento,ID',
                'detalles.*.cantidad' => 'required|integer|min:1'
            ]);

            $detalles = [];
            foreach ($request->detalles as $detalle) {
                $detalleSalida = new DetalleSalida();
                $detalleSalida->Salida_ID = $request->salida_id;
                $detalleSalida->Medicamento_ID = $detalle['medicamento_id'];
                $detalleSalida->Cantidad = $detalle['cantidad'];
                $detalleSalida->save();
                
                $detalles[] = $detalleSalida;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'detalles' => $detalles,
                'message' => 'Detalles de venta registrados exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar los detalles: ' . $e->getMessage()
            ], 500);
        }
    }
}