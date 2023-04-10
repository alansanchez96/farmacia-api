<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PharmacyController;

/**
 * /pharmacies obtiene los registros de todas las farmacias ordenadas a corde a la ubicacion
 * /pharmacy obtiene los registros de todas las farmacias ordenadas a corde a la ubicacion con un rango de 10 metros
 */
Route::apiResource('pharmacies', PharmacyController::class)->except(['update', 'destroy']);
Route::get('pharmacies-nearest', [PharmacyController::class, 'nearestPharmacies'])->name('pharmacies.nearest');
