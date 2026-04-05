<?php

use App\Http\Controllers\Api\AddressController;
use Illuminate\Support\Facades\Route;

Route::get('/regions', [AddressController::class, 'regions']);
Route::get('/provinces', [AddressController::class, 'provinces']);
Route::get('/cities', [AddressController::class, 'cities']);
Route::get('/barangays', [AddressController::class, 'barangays']);
?>
