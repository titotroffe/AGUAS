<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperadoresController;
use App\Http\Controllers\QuimicoController;
use App\Http\Controllers\JefaturaController;

Route::get('/', function () {
    return view('/welcome');
});

Route::get('/menu', function () {
    return view('menu');
})->middleware(['auth', 'verified'])->name('menu');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// Rutas para el Módulo de Operadores
Route::middleware('auth')->group(function () {
    Route::get('/operadores', [OperadoresController::class, 'index'])->name('operadores.index');
    Route::post('/operadores/presion', [OperadoresController::class, 'storePresion'])->name('operadores.storePresion');
    Route::post('/operadores/filtro', [OperadoresController::class, 'storeFiltro'])->name('operadores.storeFiltro');
    Route::post('/operadores/quimico', [OperadoresController::class, 'storeQuimico'])->name('operadores.storeQuimico');
    Route::post('/operadores/novedad', [OperadoresController::class, 'storeNovedad'])->name('operadores.storeNovedad');
    Route::post('/operadores/novedades/leidas', [OperadoresController::class, 'marcarLeidas'])->name('operadores.marcarLeidas');
    Route::delete('/operadores/presion/{id}', [OperadoresController::class, 'destroy'])->name('operadores.destroy');
    Route::delete('/operadores/filtro/{id}', [OperadoresController::class, 'destroyFiltro'])->name('operadores.destroyFiltro');
    Route::delete('/operadores/novedad/{id}', [OperadoresController::class, 'destroyNovedad'])->name('operadores.destroyNovedad');
});

// Rutas para el Módulo Químico
Route::middleware('auth')->group(function () {
    Route::get('/quimico', [QuimicoController::class, 'index'])->name('quimico.index');
    Route::post('/quimico/calidad', [QuimicoController::class, 'storeCalidad'])->name('quimico.storeCalidad');
    Route::delete('/quimico/calidad/{id}', [QuimicoController::class, 'destroyCalidad'])->name('quimico.destroyCalidad');
    Route::post('/quimico/novedad', [QuimicoController::class, 'storeNovedad'])->name('quimico.storeNovedad');
    Route::post('/quimico/novedades/leidas', [QuimicoController::class, 'marcarLeidas'])->name('quimico.marcarLeidas');
    Route::delete('/quimico/novedad/{id}', [QuimicoController::class, 'destroyNovedad'])->name('quimico.destroyNovedad');
});


// Rutas para el Módulo Jefatura
Route::middleware('auth')->group(function () {
    Route::get('/jefatura', [JefaturaController::class, 'index'])->name('jefatura.index');
});
