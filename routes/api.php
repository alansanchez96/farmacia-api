<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PharmacyController;

Route::controller(PharmacyController::class)->group(function () {
    Route::get('/pharmacies', 'index')->name('pharmacy.index');
    Route::get('/pharmacy', 'nearbyPharmacy')->name('pharmacy.nearby');
    Route::post('/pharmacy', 'store')->name('pharmacy.store');
    Route::get('/pharmacy/{id}', 'show')->name('pharmacy.show');
    Route::put('/pharmacy/{id}', 'update')->name('pharmacy.update');
    Route::delete('/pharmacy/{id}', 'delete')->name('pharmacy.delete');
});
