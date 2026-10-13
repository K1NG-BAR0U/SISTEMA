<?php

use App\Http\Controllers\FarmaciaController;
use App\Http\Controllers\SesionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BitacoraController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [SesionController::class, 'formulario'])->name('login');
    Route::post('/login', [SesionController::class, 'entrar'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SesionController::class, 'salir'])->name('logout');
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');

    Route::redirect('/', '/farmacia');
    Route::resource('farmacia', FarmaciaController::class)
        ->parameters(['farmacia' => 'registro']);
});