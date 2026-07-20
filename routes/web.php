<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventorySubmoduleController;
use App\Http\Controllers\WarehouseLayoutController;

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

// Routes for warehouse layouts submodule
Route::get('/warehouse', [WarehouseLayoutController::class, 'index'])->name('warehouse.layout');

// Keep your AJAX data API endpoints mapped below it
Route::prefix('warehouse-layout')->group(function () {
    Route::get('/data', [WarehouseLayoutController::class, 'getData'])->name('warehouse.layout.data');
    Route::post('/request', [WarehouseLayoutController::class, 'storeRequest'])->name('warehouse.layout.request');
    Route::post('/batch-request', [WarehouseLayoutController::class, 'storeBatchRequest'])->name('warehouse.layout.batch-request');
    Route::post('/process/{id}', [WarehouseLayoutController::class, 'processApproval'])->name('warehouse.layout.process');
});