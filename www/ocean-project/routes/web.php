<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/contabilidad.php';
require __DIR__.'/costos.php';
require __DIR__.'/economia.php';
require __DIR__.'/administracion.php';


Route::view('/', 'dashboard.index');