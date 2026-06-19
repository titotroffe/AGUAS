<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperadoresController;

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


Route::get('/operadores', [OperadoresController::class, 'index'])->middleware('auth');
Route::post('/operadores/presion', [OperadoresController::class, 'storePresion'])->middleware('auth');
Route::post('/operadores/filtro', [OperadoresController::class, 'storeFiltro'])->middleware('auth');
Route::post('/operadores/quimico', [OperadoresController::class, 'storeQuimico'])->middleware('auth');
Route::post('/operadores/novedad', [OperadoresController::class, 'storeNovedad'])->middleware('auth');
Route::post('/operadores/novedades/leidas', [OperadoresController::class, 'marcarLeidas'])->middleware('auth');
Route::delete('/operadores/presion/{id}', [OperadoresController::class, 'destroy'])->middleware('auth');
Route::delete('/operadores/filtro/{id}', [OperadoresController::class, 'destroyFiltro'])->middleware('auth');
Route::delete('/operadores/novedad/{id}', [OperadoresController::class, 'destroyNovedad'])->middleware('auth');

