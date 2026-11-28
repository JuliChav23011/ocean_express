<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Economia\EstudioInversionController;
use App\Http\Controllers\Economia\AlternativaInversionController;

Route::prefix('economia')->name('economia.')->group(function () {

    Route::view('/', 'economia.index')->name('index');


    Route::resource(
        'estudios',
        EstudioInversionController::class
    )->parameters([
        'estudios' => 'estudio'
    ]);


    Route::resource(
        'alternativas',
        AlternativaInversionController::class
    )->parameters([
        'alternativas' => 'alternativa'
    ]);

});
