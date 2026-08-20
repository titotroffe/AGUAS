<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperadoresController;
use App\Http\Controllers\QuimicoController;
use App\Http\Controllers\JefaturaController;

Route::get('/', function () {
    return view('/auth/login');
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

// Rutas para el Módulo Laboratorio Central
Route::middleware('auth')->group(function () {
    Route::get('/laboratorio', [\App\Http\Controllers\LaboratorioController::class, 'index'])->name('laboratorio.index');
    
    Route::post('/laboratorio/insumo', [\App\Http\Controllers\LaboratorioController::class, 'storeInsumo'])->name('laboratorio.storeInsumo');
    Route::delete('/laboratorio/insumo/{tipo}/{id}', [\App\Http\Controllers\LaboratorioController::class, 'destroyInsumo'])->name('laboratorio.destroyInsumo');
    
    Route::post('/laboratorio/agua-cruda', [\App\Http\Controllers\LaboratorioController::class, 'storeAguaCruda'])->name('laboratorio.storeAguaCruda');
    Route::delete('/laboratorio/agua-cruda/{id}', [\App\Http\Controllers\LaboratorioController::class, 'destroyAguaCruda'])->name('laboratorio.destroyAguaCruda');
    
    Route::post('/laboratorio/producto-terminado', [\App\Http\Controllers\LaboratorioController::class, 'storeProductoTerminado'])->name('laboratorio.storeProductoTerminado');
    Route::delete('/laboratorio/producto-terminado/{id}', [\App\Http\Controllers\LaboratorioController::class, 'destroyProductoTerminado'])->name('laboratorio.destroyProductoTerminado');
    
    Route::post('/laboratorio/pozo', [\App\Http\Controllers\LaboratorioController::class, 'storePozo'])->name('laboratorio.storePozo');
    Route::delete('/laboratorio/pozo/{id}', [\App\Http\Controllers\LaboratorioController::class, 'destroyPozo'])->name('laboratorio.destroyPozo');
    
    Route::post('/laboratorio/novedad', [\App\Http\Controllers\LaboratorioController::class, 'storeNovedad'])->name('laboratorio.storeNovedad');
    Route::post('/laboratorio/novedades/leidas', [\App\Http\Controllers\LaboratorioController::class, 'marcarLeidas'])->name('laboratorio.marcarLeidas');
    Route::delete('/laboratorio/novedad/{id}', [\App\Http\Controllers\LaboratorioController::class, 'destroyNovedad'])->name('laboratorio.destroyNovedad');
});
