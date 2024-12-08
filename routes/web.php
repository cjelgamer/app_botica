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

   // Dashboard
   Route::get('/admin-dashboard', function () {
       return view('admin-dashboard');
   })->name('admin.dashboard');

   
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

// Ruta para aceptar una venta (simulando la lógica de procesamiento de venta)
Route::post('/venta', function (Request $request) {
    $data = $request->all();
    // Lógica para procesar la venta
    return response()->json(['status' => 'success', 'message' => 'Venta aceptada']);
});

   Route::post('/cancelar-venta', function () {
       return response()->json(['status' => 'success', 'message' => 'Venta cancelada']);
   });
});

use App\Http\Controllers\SalidaController;
use App\Http\Controllers\DetalleSalidaController;

// Rutas para Salida
Route::prefix('salida')->group(function () {
    Route::get('/', [SalidaController::class, 'index']);           // Listar todas las salidas
    Route::get('/{id}', [SalidaController::class, 'show']);        // Mostrar una salida específica
    Route::post('/', [SalidaController::class, 'store']);          // Crear una nueva salida
    Route::put('/{id}', [SalidaController::class, 'update']);      // Actualizar una salida
    Route::delete('/{id}', [SalidaController::class, 'destroy']);  // Eliminar una salida
});

// Rutas para DetalleSalida
Route::prefix('detalle-salida')->group(function () {
    Route::get('/', [DetalleSalidaController::class, 'index']);           // Listar todos los detalles de salidas
    Route::get('/{id}', [DetalleSalidaController::class, 'show']);        // Mostrar un detalle específico
    Route::post('/', [DetalleSalidaController::class, 'store']);          // Crear un nuevo detalle
    Route::put('/{id}', [DetalleSalidaController::class, 'update']);      // Actualizar un detalle
    Route::delete('/{id}', [DetalleSalidaController::class, 'destroy']);  // Eliminar un detalle
});
use App\Http\Controllers\ClienteController;

Route::prefix('cliente')->group(function () {
    // Ruta para guardar o actualizar un cliente (por DNI)
    Route::post('/', [ClienteController::class, 'guardarCliente'])->name('cliente.guardar');
});
