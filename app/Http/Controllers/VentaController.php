<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\Medicamento;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function store(Request $request)
    {
        // Validación de la solicitud
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:vendedores,id',
            'productos' => 'required|array',
        ]);

        // Crear la venta en la tabla salida
        $venta = Salida::create([
            'cliente_id' => $validatedData['cliente_id'],
            'vendedor_id' => $validatedData['vendedor_id'],
            'fecha' => now(),
            'total' => 0, // Se calculará después
        ]);

        $total = 0;
        foreach ($validatedData['productos'] as $producto) {
            $medicamento = Medicamento::find($producto['medicamento_id']);
            $precio_total = $producto['cantidad'] * $medicamento->Precio;
            $total += $precio_total;

            // Crear el detalle de la venta en la tabla detalle_salida
            DetalleSalida::create([
                'salida_id' => $venta->id,
                'medicamento_id' => $producto['medicamento_id'],
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $medicamento->Precio,
                'precio_total' => $precio_total,
            ]);

            // Actualizar el stock del medicamento
            $medicamento->decrement('Stock', $producto['cantidad']);
        }

        // Actualizar el total de la venta
        $venta->update(['total' => $total]);

        return response()->json(['message' => 'Venta realizada con éxito', 'venta' => $venta], 201);
    }

}
