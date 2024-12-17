<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ReporteController extends Controller
{
    public function generarReporteCompras(Request $request)
    {
        try {
            $tipo = $request->tipo ?? 'todo';
            
            $entradasMedicamentos = match($tipo) {
                'semana' => DB::table('vista_reporte_compras_semana')->get(),
                'mes' => DB::table('vista_reporte_compras_mes')->get(),
                default => DB::table('vista_reporte_compras')->get(),
            };

            if ($entradasMedicamentos->isEmpty()) {
                return response()->json([
                    'message' => 'No hay datos disponibles para generar el reporte'
                ], 404);
            }

            $pdf = PDF::loadView('reporte_compras', [
                'entradasMedicamentos' => $entradasMedicamentos,
                'fecha' => now()->format('d/m/Y'),
                'tipo' => $tipo
            ]);
            
            $filename = match($tipo) {
                'semana' => 'reporte_compras_semana.pdf',
                'mes' => 'reporte_compras_mes.pdf',
                default => 'reporte_compras_completo.pdf',
            };
            
            return $pdf->download($filename);

        } catch (Exception $e) {
            \Log::error('Error generando reporte de compras: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }



    public function generarReporteCajas(Request $request, $fecha = null)
    {
        try {
            $fechaReporte = $fecha ?? now()->format('Y-m-d');
            
            $cajas = DB::select('CALL sp_reporte_cajas_fecha(?)', [$fechaReporte]);
            
            if (empty($cajas)) {
                return response()->json([
                    'message' => 'No hay datos de cajas disponibles para la fecha seleccionada'
                ], 404);
            }
    
            $pdf = PDF::loadView('reporte_cajas', [
                'cajas' => $cajas,
                'fecha' => date('d/m/Y', strtotime($fechaReporte))
            ]);
            
            return $pdf->download('reporte_cajas_' . $fechaReporte . '.pdf');
    
        } catch (Exception $e) {
            \Log::error('Error generando reporte de cajas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getDatosCaja($fecha)
{
    try {
        $cajas = DB::select('CALL sp_reporte_cajas_fecha(?)', [$fecha]);
        return response()->json($cajas);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'Error al obtener datos de caja: ' . $e->getMessage()
        ], 500);
    }
}
}