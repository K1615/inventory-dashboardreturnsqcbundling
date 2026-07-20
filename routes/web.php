<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventorySubmoduleController;
use App\Http\Controllers\StockMovementController;

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

// Routes for stock movement submodule
Route::get('/movement', [StockMovementController::class, 'index'])->name('stock-movements.index');

Route::get('/api/stock-movements/data', [StockMovementController::class, 'getDashboardData'])->name('stock-movements.data');
Route::post('/api/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
Route::patch('/api/stock-movements/{txId}/status', [StockMovementController::class, 'updateStatus'])->name('stock-movements.update-status');