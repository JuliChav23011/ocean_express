<?php

use App\Http\Controllers\Contabilidad\AsientoContableController;
use App\Http\Controllers\Contabilidad\CierreContableController;
use App\Http\Controllers\Contabilidad\ContabilidadController;
use App\Http\Controllers\Contabilidad\CuentaContableController;
use App\Http\Controllers\Contabilidad\LibroContableController;
use App\Http\Controllers\Contabilidad\PeriodoContableController;
use App\Http\Controllers\Contabilidad\ReporteFinancieroController;
use Illuminate\Support\Facades\Route;

Route::prefix('contabilidad')->name('contabilidad.')->group(function () {
    // index contabilidad
    Route::get('/', [ContabilidadController::class, 'index'])->name('index');

    // estas son las rutas del catalog de cuentas
    Route::resource('cuentas', CuentaContableController::class)->except(['show']);
    Route::patch('cuentas/{cuenta}/toggle-estado', [CuentaContableController::class, 'toggleEstado'])->name('cuentas.toggle');

    // estas rutas son para los periodos contables
    Route::get('periodos', [PeriodoContableController::class, 'index'])->name('periodos.index');
    Route::post('periodos', [PeriodoContableController::class, 'store'])->name('periodos.store');
    Route::put('periodos/{periodo}', [PeriodoContableController::class, 'update'])->name('periodos.update');
    Route::patch('periodos/{periodo}/estado', [PeriodoContableController::class, 'cambiarEstado'])->name('periodos.estado');

    // aqui estan los de asientos, detalles, ajustes y evidencias
    Route::get('asientos', [AsientoContableController::class, 'index'])->name('asientos.index');
    Route::get('asientos/crear', [AsientoContableController::class, 'create'])->name('asientos.create');
    Route::post('asientos', [AsientoContableController::class, 'store'])->name('asientos.store');
    Route::get('asientos/{asiento}', [AsientoContableController::class, 'show'])->name('asientos.show');
    Route::get('asientos/{asiento}/editar', [AsientoContableController::class, 'edit'])->name('asientos.edit');
    Route::put('asientos/{asiento}', [AsientoContableController::class, 'update'])->name('asientos.update');
    Route::patch('asientos/{asiento}/contabilizar', [AsientoContableController::class, 'contabilizar'])->name('asientos.contabilizar');
    Route::patch('asientos/{asiento}/anular', [AsientoContableController::class, 'anular'])->name('asientos.anular');
    Route::post('asientos/{asiento}/evidencia', [AsientoContableController::class, 'asociarEvidencia'])->name('asientos.evidencia');
    Route::get('evidencias/{evidencia}/descargar', [AsientoContableController::class, 'descargarEvidencia'])->name('evidencias.descargar');

    // vista dedicada de Ajustes Contables
    Route::get('ajustes', [AsientoContableController::class, 'ajustes'])->name('ajustes.index');

    // libros contables
    Route::get('libro-diario', [LibroContableController::class, 'diario'])->name('libros.diario');
    Route::get('libro-mayor', [LibroContableController::class, 'mayor'])->name('libros.mayor');
    Route::get('balance-comprobacion', [LibroContableController::class, 'balanceComprobacion'])->name('libros.balance');

    // 8. Estados financieros
    Route::get('estado-resultados', [ReporteFinancieroController::class, 'estadoResultados'])->name('reportes.resultados');
    Route::get('balance-general', [ReporteFinancieroController::class, 'balanceGeneral'])->name('reportes.balance');
    Route::get('cambios-patrimonio', [ReporteFinancieroController::class, 'cambiosPatrimonio'])->name('reportes.patrimonio');
    Route::get('flujo-efectivo', [ReporteFinancieroController::class, 'flujoEfectivo'])->name('reportes.flujo');

    // 9. Cierre contable
    Route::get('cierre', [CierreContableController::class, 'index'])->name('cierre.index');
    Route::post('cierre/{periodo}/ejecutar', [CierreContableController::class, 'ejecutar'])->name('cierre.ejecutar');
});