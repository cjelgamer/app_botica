<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\VendedorLoginController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\DetalleEntradaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\DetalleSalidaController;
use App\Http\Controllers\ClienteController;
use App\Models\Medicamento;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación (sin middleware)
Route::get('/vendedor/login', [VendedorLoginController::class, 'showLoginForm'])->name('vendedor.login');
Route::post('/vendedor/login', [VendedorLoginController::class, 'login']);


// Rutas protegidas
Route::group(['middleware' => ['web']], function () {
    // Logout y perfil
    Route::post('/vendedor/logout', [VendedorLoginController::class, 'logout'])->name('vendedor.logout');
    Route::get('/vendedor/perfil', [VendedorLoginController::class, 'getPerfil']);

    // Dashboard Admin
    Route::get('/admin-dashboard', function () {
        return view('admin-dashboard');
    })->name('admin.dashboard');

    Route::get('/vendedor-dashboard', function () {
        return view('vendedor-dashboard');
    })->name('vendedor.dashboard');

    Route::get('/vendedor-dashboard/{any}', function () {
        return view('vendedor-dashboard');
    })->where('any', '.*');
    
    // Rutas para vendedores
    Route::get('/vendedores', [VendedorController::class, 'index']);
    Route::get('/vendedores/{id}', [VendedorController::class, 'show']);
    Route::post('/vendedores', [VendedorController::class, 'store']);
    Route::put('/vendedores/{id}', [VendedorController::class, 'update']);
    Route::delete('/vendedores/{id}', [VendedorController::class, 'destroy']);
    Route::put('/vendedores/{id}/cambiar-contrasena', [VendedorController::class, 'updatePassword']);
    Route::put('/vendedores/{id}/actualizar-estado', [VendedorController::class, 'updateEstado']);

    // Rutas para laboratorios
    Route::get('/laboratorios', [LaboratorioController::class, 'index'])->name('laboratorios.index');
    Route::get('/laboratorios/{id}', [LaboratorioController::class, 'show'])->name('laboratorios.show');
    Route::post('/laboratorios', [LaboratorioController::class, 'store'])->name('laboratorios.store');
    Route::put('/laboratorios/{id}', [LaboratorioController::class, 'update'])->name('laboratorios.update');
    Route::delete('/laboratorios/{id}', [LaboratorioController::class, 'destroy'])->name('laboratorios.destroy');

    // Rutas para medicamentos
    Route::get('/medicamentos', [MedicamentoController::class, 'index']);
    Route::get('/medicamentos/{id}', [MedicamentoController::class, 'show']);
    Route::post('/medicamentos', [MedicamentoController::class, 'store']);
    Route::put('/medicamentos/{id}', [MedicamentoController::class, 'update']);
    Route::delete('/medicamentos/{id}', [MedicamentoController::class, 'destroy']);

    // Rutas para entradas y detalles
    Route::post('/entrada', [EntradaController::class, 'guardarEntrada']);
    Route::post('/detalle-entrada', [DetalleEntradaController::class, 'agregarDetalle']);

    Route::get('/generar-reporte-compras/{tipo}', [ReporteController::class, 'generarReporteCompras'])
        ->name('reporte.compras.generar');

    // Ruta para obtener los medicamentos (para búsqueda)
    Route::get('/medicamentos', function (Request $request) {
        $searchQuery = $request->input('search');
        $medicamentos = Medicamento::where('nombre', 'like', '%' . $searchQuery . '%')->get();
        return response()->json($medicamentos);
    });


    Route::post('/clientes', [ClienteController::class, 'store']);
Route::get('/clientes/buscar/{dni}', [ClienteController::class, 'buscarPorDNI']);
Route::get('/clientes', [ClienteController::class, 'index']);

Route::post('/salida', [SalidaController::class, 'store']);
Route::post('/detalle-salida', [DetalleSalidaController::class, 'store']);
Route::post('/detalle-salida/masivo', [DetalleSalidaController::class, 'storeMasivo']);
});

// Rutas para Salida
