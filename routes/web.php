<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperadoresController;
use App\Http\Controllers\QuimicoController;
use App\Http\Controllers\JefaturaController;
use App\Http\Controllers\BombasController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('menu');
    }
    return view('auth.login');
});

Route::get('/menu', function () {
    return view('menu');
})->middleware(['auth'])->name('menu');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// Rutas para el Modulo de Operadores
Route::middleware(['auth', 'role:operador'])->group(function () {
    Route::get('/operadores', [OperadoresController::class, 'index'])->name('operadores.index');
    // QA-08: throttle en rutas de escritura - max. 20 envios/min por usuario
    Route::post('/operadores/presion',            [OperadoresController::class, 'storePresion'])->middleware('throttle:20,1')->name('operadores.storePresion');
    Route::post('/operadores/filtro',             [OperadoresController::class, 'storeFiltro'])->middleware('throttle:20,1')->name('operadores.storeFiltro');
    Route::post('/operadores/quimico',            [OperadoresController::class, 'storeQuimico'])->middleware('throttle:20,1')->name('operadores.storeQuimico');
    Route::post('/operadores/novedad',            [OperadoresController::class, 'storeNovedad'])->middleware('throttle:10,1')->name('operadores.storeNovedad');
    Route::post('/operadores/novedades/leidas',   [OperadoresController::class, 'marcarLeidas'])->middleware('throttle:10,1')->name('operadores.marcarLeidas');
    Route::delete('/operadores/presion/{id}',     [OperadoresController::class, 'destroy'])->middleware('throttle:10,1')->name('operadores.destroy');
    Route::delete('/operadores/filtro/{id}',      [OperadoresController::class, 'destroyFiltro'])->middleware('throttle:10,1')->name('operadores.destroyFiltro');
    Route::delete('/operadores/novedad/{id}',     [OperadoresController::class, 'destroyNovedad'])->middleware('throttle:10,1')->name('operadores.destroyNovedad');
});

// Rutas para el Modulo Quimico
Route::middleware(['auth', 'role:quimico'])->group(function () {
    Route::get('/quimico', [QuimicoController::class, 'index'])->name('quimico.index');
    // QA-08: throttle en rutas de escritura
    Route::post('/quimico/calidad',                   [QuimicoController::class, 'storeCalidad'])->middleware('throttle:20,1')->name('quimico.storeCalidad');
    Route::delete('/quimico/calidad/{id}',            [QuimicoController::class, 'destroyCalidad'])->middleware('throttle:10,1')->name('quimico.destroyCalidad');
    Route::post('/quimico/bacteriologico',            [QuimicoController::class, 'storeBacteriologico'])->middleware('throttle:20,1')->name('quimico.storeBacteriologico');
    Route::delete('/quimico/bacteriologico/{id}',     [QuimicoController::class, 'destroyBacteriologico'])->middleware('throttle:10,1')->name('quimico.destroyBacteriologico');
    Route::post('/quimico/novedad',                   [QuimicoController::class, 'storeNovedad'])->middleware('throttle:10,1')->name('quimico.storeNovedad');
    Route::post('/quimico/novedades/leidas',          [QuimicoController::class, 'marcarLeidas'])->middleware('throttle:10,1')->name('quimico.marcarLeidas');
    Route::delete('/quimico/novedad/{id}',            [QuimicoController::class, 'destroyNovedad'])->middleware('throttle:10,1')->name('quimico.destroyNovedad');
    Route::post('/quimico/caudalimetro',              [QuimicoController::class, 'storeCaudalimetro'])->middleware('throttle:20,1')->name('quimico.storeCaudalimetro');
    Route::delete('/quimico/caudalimetro/{id}',       [QuimicoController::class, 'destroyCaudalimetro'])->middleware('throttle:10,1')->name('quimico.destroyCaudalimetro');
});


// Rutas para el Modulo Jefatura
Route::middleware(['auth', 'role:jefatura,admin'])->group(function () {
    Route::get('/jefatura', [JefaturaController::class, 'index'])->name('jefatura.index');
    // QA-08: throttle en gestion de usuarios
    Route::post('/jefatura/aprobar-usuario/{id}',    [JefaturaController::class, 'aprobarUsuario'])->middleware('throttle:10,1')->name('jefatura.aprobarUsuario');
    Route::delete('/jefatura/rechazar-usuario/{id}', [JefaturaController::class, 'rechazarUsuario'])->middleware('throttle:10,1')->name('jefatura.rechazarUsuario');
    Route::put('/jefatura/actualizar-rol/{id}',      [JefaturaController::class, 'actualizarRol'])->middleware('throttle:10,1')->name('jefatura.actualizarRol');
    Route::delete('/jefatura/dar-de-baja/{id}',      [JefaturaController::class, 'darDeBaja'])->middleware('throttle:10,1')->name('jefatura.darDeBaja');
    Route::post('/jefatura/reactivar-usuario/{id}',  [JefaturaController::class, 'reactivarUsuario'])->middleware('throttle:10,1')->name('jefatura.reactivarUsuario');

    // ABM Dinamico - QA-08: throttle mas permisivo para uso administrativo
    Route::get('/jefatura/abm',                           [\App\Http\Controllers\AbmController::class, 'index'])->name('jefatura.abm.index');
    Route::get('/jefatura/abm/{table}',                   [\App\Http\Controllers\AbmController::class, 'showTable'])->name('jefatura.abm.show');
    Route::post('/jefatura/abm/{table}',                  [\App\Http\Controllers\AbmController::class, 'store'])->middleware('throttle:30,1')->name('jefatura.abm.store');
    Route::put('/jefatura/abm/{table}/{id}',              [\App\Http\Controllers\AbmController::class, 'update'])->middleware('throttle:30,1')->name('jefatura.abm.update');
    Route::delete('/jefatura/abm/{table}/{id}',           [\App\Http\Controllers\AbmController::class, 'destroy'])->middleware('throttle:30,1')->name('jefatura.abm.destroy');
    Route::post('/jefatura/abm/{table}/column',           [\App\Http\Controllers\AbmController::class, 'addColumn'])->middleware('throttle:10,1')->name('jefatura.abm.addColumn');
    Route::put('/jefatura/abm/{table}/column/{column}',   [\App\Http\Controllers\AbmController::class, 'updateColumn'])->middleware('throttle:10,1')->name('jefatura.abm.updateColumn');
    Route::delete('/jefatura/abm/{table}/column/{column}',[\App\Http\Controllers\AbmController::class, 'destroyColumn'])->middleware('throttle:10,1')->name('jefatura.abm.destroyColumn');
});

// Rutas para Bombas y Pozos
Route::middleware(['auth', 'role'])->group(function () {
    Route::get('/bombas/estado',  [BombasController::class, 'estado'])->name('bombas.estado');
    // QA-08: throttle 30/min para acomodar polling + acciones manuales
    Route::post('/bombas/toggle', [BombasController::class, 'toggle'])->middleware('throttle:30,1')->name('bombas.toggle');
});

// Rutas para el Modulo Laboratorio Central
Route::middleware(['auth', 'role:laboratorio'])->group(function () {
    Route::get('/laboratorio', [\App\Http\Controllers\LaboratorioController::class, 'index'])->name('laboratorio.index');

    // QA-08: throttle en todas las rutas de escritura de laboratorio
    Route::post('/laboratorio/insumo',                    [\App\Http\Controllers\LaboratorioController::class, 'storeInsumo'])->middleware('throttle:20,1')->name('laboratorio.storeInsumo');
    Route::delete('/laboratorio/insumo/{tipo}/{id}',      [\App\Http\Controllers\LaboratorioController::class, 'destroyInsumo'])->middleware('throttle:10,1')->name('laboratorio.destroyInsumo');

    Route::post('/laboratorio/agua-cruda',                [\App\Http\Controllers\LaboratorioController::class, 'storeAguaCruda'])->middleware('throttle:20,1')->name('laboratorio.storeAguaCruda');
    Route::delete('/laboratorio/agua-cruda/{id}',         [\App\Http\Controllers\LaboratorioController::class, 'destroyAguaCruda'])->middleware('throttle:10,1')->name('laboratorio.destroyAguaCruda');

    Route::post('/laboratorio/producto-terminado',        [\App\Http\Controllers\LaboratorioController::class, 'storeProductoTerminado'])->middleware('throttle:20,1')->name('laboratorio.storeProductoTerminado');
    Route::delete('/laboratorio/producto-terminado/{id}', [\App\Http\Controllers\LaboratorioController::class, 'destroyProductoTerminado'])->middleware('throttle:10,1')->name('laboratorio.destroyProductoTerminado');

    Route::post('/laboratorio/agua-red',                  [\App\Http\Controllers\LaboratorioController::class, 'storeAguaRed'])->middleware('throttle:20,1')->name('laboratorio.storeAguaRed');
    Route::delete('/laboratorio/agua-red/{id}',           [\App\Http\Controllers\LaboratorioController::class, 'destroyAguaRed'])->middleware('throttle:10,1')->name('laboratorio.destroyAguaRed');

    Route::post('/laboratorio/pozo',                      [\App\Http\Controllers\LaboratorioController::class, 'storePozo'])->middleware('throttle:20,1')->name('laboratorio.storePozo');
    Route::delete('/laboratorio/pozo/{id}',               [\App\Http\Controllers\LaboratorioController::class, 'destroyPozo'])->middleware('throttle:10,1')->name('laboratorio.destroyPozo');

    Route::post('/laboratorio/control-escriturado',        [\App\Http\Controllers\LaboratorioController::class, 'storeControlEscriturado'])->middleware('throttle:20,1')->name('laboratorio.storeControlEscriturado');
    Route::delete('/laboratorio/control-escriturado/{id}', [\App\Http\Controllers\LaboratorioController::class, 'destroyControlEscriturado'])->middleware('throttle:10,1')->name('laboratorio.destroyControlEscriturado');

    Route::post('/laboratorio/control-tanques',           [\App\Http\Controllers\LaboratorioController::class, 'storeControlTanques'])->middleware('throttle:20,1')->name('laboratorio.storeControlTanques');
    Route::delete('/laboratorio/control-tanques/{id}',    [\App\Http\Controllers\LaboratorioController::class, 'destroyControlTanques'])->middleware('throttle:10,1')->name('laboratorio.destroyControlTanques');

    Route::post('/laboratorio/novedad',                   [\App\Http\Controllers\LaboratorioController::class, 'storeNovedad'])->middleware('throttle:10,1')->name('laboratorio.storeNovedad');
    Route::post('/laboratorio/novedades/leidas',          [\App\Http\Controllers\LaboratorioController::class, 'marcarLeidas'])->middleware('throttle:10,1')->name('laboratorio.marcarLeidas');
    Route::delete('/laboratorio/novedad/{id}',            [\App\Http\Controllers\LaboratorioController::class, 'destroyNovedad'])->middleware('throttle:10,1')->name('laboratorio.destroyNovedad');
});
