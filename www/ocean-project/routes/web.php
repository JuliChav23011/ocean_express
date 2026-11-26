<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administracion\DashboardController;

require __DIR__.'/contabilidad.php';
require __DIR__.'/costos.php';
require __DIR__.'/economia.php';
require __DIR__.'/administracion.php';


Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');