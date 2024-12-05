<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendedor;

class VendedorLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string',
            'Contraseña' => 'required|string',
        ]);
    
        $credentials = [
            'Nombre' => $request->Nombre,
            'password' => $request->Contraseña,
        ];
    
        if (Auth::guard('vendedor')->attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::guard('vendedor')->user();
                
            // Guardar en la sesión correctamente
            $request->session()->put([
                'user_id' => $user->id,  // Esto actualiza la columna user_id en sessions
                'vendedor_id' => $user->id,
                'vendedor_nombre' => $user->Nombre,
                'vendedor_rol' => $user->Rol,
            ]);
    
            return response()->json([
                'success' => true,
                'role' => $user->Rol,
                'vendedor_id' => $user->id,
                'nombre' => $user->Nombre,
                'telefono' => $user->Telefono
            ]);
        }
    
        return response()->json([
            'success' => false, 
            'message' => 'Credenciales incorrectas'
        ], 401);
    }
    
    public function getPerfil(Request $request)
    {
        // Verificar si el vendedor está autenticado
        $vendedor = Auth::guard('vendedor')->user();
    
        if (!$vendedor) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }
    
        // Si está autenticado, devolver la información del vendedor
        return response()->json([
            'vendedor_id' => $vendedor->id,
            'nombre' => $vendedor->Nombre,
            'telefono' => $vendedor->Telefono,
            'rol' => $vendedor->Rol,
            'estado' => $vendedor->Estado  // Agregamos el estado
        ]);
    }


    public function logout(Request $request)
{
    try {
        // Limpiar datos específicos de la sesión
        $request->session()->forget([
            'user_id',
            'vendedor_id',
            'vendedor_nombre',
            'vendedor_rol'
        ]);
        
        // Cerrar la sesión de autenticación
        Auth::guard('vendedor')->logout();
        
        // Invalidar la sesión
        $request->session()->invalidate();
        
        // Regenerar el token
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al cerrar sesión: ' . $e->getMessage()
        ], 500);
    }
}
    
    
}


//para usar el id del vendedor en cualquier controlador:
//$vendedorId = session('vendedor_id');
//usando auth
//$vendedorId = Auth::user()->id;