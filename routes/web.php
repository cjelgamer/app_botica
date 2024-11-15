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