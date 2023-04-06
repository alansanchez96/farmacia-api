<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PharmacyController;

/**
 * /pharmacies obtiene los registros de todas las farmacias ordenadas a corde a la ubicacion
 * /pharmacy obtiene los registros de todas las farmacias ordenadas a corde a la ubicacion con un rango de 10 metros
 */
Route::controller(PharmacyController::class)->group(function () {
    Route::get('/pharmacies', 'index')->name('pharmacies.index');
    Route::get('/pharmacy', 'getNearestPharmacy')->name('pharmacy.nearest');
    Route::post('/pharmacy', 'store')->name('pharmacy.store');
    Route::get('/pharmacy/{id}', 'show')->name('pharmacy.show');
    Route::put('/pharmacy/{id}', 'update')->name('pharmacy.update');
    Route::delete('/pharmacy/{id}', 'delete')->name('pharmacy.delete');
});
