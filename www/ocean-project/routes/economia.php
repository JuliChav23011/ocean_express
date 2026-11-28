<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Economia\EstudioInversionController;

Route::prefix('economia')->name('economia.')->group(function () {

    Route::view('/', 'economia.index')->name('index');

    Route::resource(
        'estudios',
        EstudioInversionController::class
    )->parameters([
        'estudios' => 'estudio'
    ]);

});
