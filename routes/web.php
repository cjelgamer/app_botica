<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Auth\VendedorLoginController;


Route::get('/vendedor/login', [VendedorLoginController::class, 'showLoginForm'])->name('vendedor.login');
Route::post('/vendedor/login', [VendedorLoginController::class, 'login']);


use App\Http\Controllers\VendedorController;
//rutas para vendedores list
Route::get('/vendedores', [VendedorController::class, 'index']);
Route::get('/vendedores/{id}', [VendedorController::class, 'show']);
Route::post('/vendedores', [VendedorController::class, 'store']);
Route::put('/vendedores/{id}', [VendedorController::class, 'update']);
Route::delete('/vendedores/{id}', [VendedorController::class, 'destroy']);
Route::put('/vendedores/{id}/cambiar-contrasena', [VendedorController::class, 'updatePassword']);
Route::put('/vendedores/{id}/actualizar-estado', [VendedorController::class, 'updateEstado']);


//rutas para laboratorios
use App\Http\Controllers\LaboratorioController;

// Ruta para listar laboratorios
Route::get('/laboratorios', [LaboratorioController::class, 'index'])->name('laboratorios.index');
// Ruta para mostrar un laboratorio por ID
Route::get('/laboratorios/{id}', [LaboratorioController::class, 'show'])->name('laboratorios.show');
// Ruta para crear un nuevo laboratorio
Route::post('/laboratorios', [LaboratorioController::class, 'store'])->name('laboratorios.store');
// Ruta para actualizar un laboratorio existente
Route::put('/laboratorios/{id}', [LaboratorioController::class, 'update'])->name('laboratorios.update');
// Ruta para eliminar un laboratorio
Route::delete('/laboratorios/{id}', [LaboratorioController::class, 'destroy'])->name('laboratorios.destroy');



use App\Http\Controllers\MedicamentoController;

Route::get('/medicamentos', [MedicamentoController::class, 'index']);
Route::get('/medicamentos/{id}', [MedicamentoController::class, 'show']);
Route::post('/medicamentos', [MedicamentoController::class, 'store']);
Route::put('/medicamentos/{id}', [MedicamentoController::class, 'update']);
Route::delete('/medicamentos/{id}', [MedicamentoController::class, 'destroy']);