<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalidaController extends Controller
{
    // Crear una nueva venta (Salida)
    public function store(Request $request)
    {
        // Verificar que el vendedor esté autenticado
        $vendedorId = Auth::id();

        if (!$vendedorId) {
            return response()->json(['error' => 'El vendedor no está autenticado'], 401);
        }

        // Validar los datos del formulario de la venta
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',  // ID del cliente
            'monto_total' => 'required|numeric|min:0',      // Monto total de la venta
            'Tipo_de_Pago' => 'required|string|max:50',     // Tipo de pago
            'fecha_venta' => 'required|date',                // Fecha de la venta
        ]);

        try {
            // Crear la salida (venta) en la base de datos
            $salida = Salida::create([
                'cliente_id' => $validated['cliente_id'],
                'vendedor_id' => $vendedorId, // Asignar el ID del vendedor desde la sesión
                'monto_total' => $validated['monto_total'],
                'tipo_pago' => $validated['Tipo_de_Pago'],
                'fecha_venta' => $validated['fecha_venta'],
            ]);

            // Si se envían detalles de medicamentos, guardarlos
            if (isset($validated['detalles'])) {
                foreach ($validated['detalles'] as $detalle) {
                    $salida->detalles()->create([
                        'medicamento_id' => $detalle['medicamento_id'],
                        'cantidad' => $detalle['cantidad'],
                    ]);
                }
            }

            // Retornar una respuesta exitosa con la venta y sus detalles
            return response()->json([
                'message' => 'Venta registrada exitosamente',
                'salida' => $salida,
                'detalles' => $salida->detalles,
            ], 201); // Código de estado 201 para creado exitosamente
        } catch (\Exception $e) {
            // Loguear el error si ocurre un problema al guardar la venta
            \Log::error('Error al guardar la venta:', ['exception' => $e->getMessage()]);

            // Retornar una respuesta de error
            return response()->json([
                'error' => 'No se pudo registrar la venta.',
                'details' => $e->getMessage(),
            ], 500); // Código de estado 500 para error interno del servidor
        }
    }
}
