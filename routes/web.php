<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventorySubmoduleController;

// Route the root URL directly to the Inventory Dashboard
Route::get('/', [InventorySubmoduleController::class, 'index'])->name('inventory.dashboard');

// Keep the API routes grouped for your fetch calls
Route::prefix('inventory/api')->controller(InventorySubmoduleController::class)->group(function () {
    Route::get('/state', 'getState');
    Route::post('/inspection', 'submitInspection');
    Route::post('/rma', 'submitRma');
    Route::post('/resolve-return', 'resolveReturn');
    Route::post('/bundle', 'submitBundle');
    Route::post('/resolve-bundle', 'resolveBundle');
});
