<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class ReporteController extends Controller
{
    public function generarReporteCompras()
    {
        $entradasMedicamentos = DB::table('Medicamento as m')
            ->join('Detalle_Entrada as de', 'm.ID', '=', 'de.Medicamento_ID')
            ->join('Entrada as e', 'de.Entrada_ID', '=', 'e.ID')
            ->join('Laboratorio as l', 'e.Laboratorio_ID', '=', 'l.ID')
            ->select('m.Nombre as Medicamento', 'l.Nombre as Laboratorio', 'e.Fecha_Entrega', 'de.Cantidad', 'e.Total as Total')
            ->orderBy('e.Fecha_Entrega', 'desc')
            ->get();
    
        $pdf = PDF::loadView('reporte_compras', compact('entradasMedicamentos'));
        return $pdf->download('reporte_compras.pdf');
    }
}