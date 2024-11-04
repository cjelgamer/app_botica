<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendedorLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validar la entrada
        $request->validate([
            'Nombre' => 'required|string',
            'Contraseña' => 'required|string',
        ]);
    
        $credentials = [
            'Nombre' => $request->Nombre,
            'password' => $request->Contraseña, // Asegúrate de que el campo sea 'password'
        ];
    
        // Intentar autenticar al vendedor
        if (Auth::guard('vendedor')->attempt($credentials)) {
            return response()->json(['success' => true], 200); // Respuesta exitosa
        }
    
        // Si no hay coincidencias, devuelve un error
        return response()->json(['success' => false, 'error' => 'Las credenciales no coinciden con nuestros registros.'], 401);
    }
    
    
}
