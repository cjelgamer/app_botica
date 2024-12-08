<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ReporteController extends Controller
{
    public function generarReporteCompras()
{
    try {
        $entradasMedicamentos = DB::table('vista_reporte_compras')->get();

        if ($entradasMedicamentos->isEmpty()) {
            return response()->json([
                'message' => 'No hay datos disponibles para generar el reporte'
            ], 404);
        }

        $pdf = PDF::loadView('reporte_compras', [
            'entradasMedicamentos' => $entradasMedicamentos,
            'fecha' => now()->format('d/m/Y')
        ]);
        
        return $pdf->download('reporte_compras.pdf');

    } catch (Exception $e) {
        \Log::error('Error generando reporte de compras: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error al generar el reporte: ' . $e->getMessage()
        ], 500);
    }
}
}