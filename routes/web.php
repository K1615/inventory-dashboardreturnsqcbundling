<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventorySubmoduleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WarehouseLayoutController;
use App\Http\Controllers\InventoryController;

// Route the root URL directly to the Inventory Dashboard
Route::get('/', [InventorySubmoduleController::class, 'index'])->name('inventory.dashboard');

// Keep the API routes grouped for your fetch calls
Route::prefix('inventory/api')->controller(InventorySubmoduleController::class)->group(function () {
    Route::get('/state', 'getState');
    Route::get('/alerts-summary', 'alertsSummary')->name('alerts.summary');
    Route::post('/inspection', 'submitInspection');
    Route::post('/rma', 'submitRma');
    Route::post('/resolve-return', 'resolveReturn');
    Route::post('/bundle', 'submitBundle');
    Route::post('/resolve-bundle', 'resolveBundle');

    // Alerts & Reorders submodule
    Route::post('/limits/{id}', 'updateItemLimits');
    Route::post('/auto-reorder/{id}', 'toggleAutoReorder');
    Route::post('/alerts/{id}/acknowledge', 'acknowledgeAlert');
    Route::post('/alerts/{id}/resolve', 'resolveAlert');
    Route::post('/submit-po', 'submitPO');
    Route::post('/draft/{id}/submit', 'submitDraft');
    Route::post('/draft/{id}/discard', 'discardDraft');
    Route::post('/pipeline/{id}', 'processPipeline');
    Route::post('/pipeline/{id}/receive', 'markReceived');
});

// Routes for stock movement submodule
Route::get('/movement', [StockMovementController::class, 'index'])->name('stock-movements.index');

Route::get('/api/stock-movements/data', [StockMovementController::class, 'getDashboardData'])->name('stock-movements.data');
Route::post('/api/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
Route::patch('/api/stock-movements/{txId}/status', [StockMovementController::class, 'updateStatus'])->name('stock-movements.update-status');
// Routes for warehouse layouts submodule
Route::get('/warehouse', [WarehouseLayoutController::class, 'index'])->name('warehouse.layout');

// Keep your AJAX data API endpoints mapped below it
Route::prefix('warehouse-layout')->group(function () {
    Route::get('/data', [WarehouseLayoutController::class, 'getData'])->name('warehouse.layout.data');
    Route::post('/request', [WarehouseLayoutController::class, 'storeRequest'])->name('warehouse.layout.request');
    Route::post('/batch-request', [WarehouseLayoutController::class, 'storeBatchRequest'])->name('warehouse.layout.batch-request');
    Route::post('/process/{id}', [WarehouseLayoutController::class, 'processApproval'])->name('warehouse.layout.process');
});
// Routes for inventory items submodule
Route::get('/items', [InventoryController::class, 'index'])->name('inventory.index');

// API Storage Data Mutations
Route::post('/api/requests', [InventoryController::class, 'storeRequest'])->name('api.requests.store');
Route::post('/api/requests/{id}/resolve', [InventoryController::class, 'resolveRequest'])->name('api.requests.resolve');
