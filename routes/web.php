<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Auth\VendedorLoginController;

Route::get('/vendedor/login', [VendedorLoginController::class, 'showLoginForm'])->name('vendedor.login');
Route::post('/vendedor/login', [VendedorLoginController::class, 'login']);