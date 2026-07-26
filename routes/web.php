<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventorySubmoduleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WarehouseLayoutController;
use App\Http\Controllers\InventoryController;

// Route the root URL directly to the Inventory Dashboard
// URL: http://inventory-dashboardreturnsqcbundling.test/
Route::get('/', [InventorySubmoduleController::class, 'index'])->name('inventory.dashboard');

// Keep the API routes grouped for your fetch calls
Route::prefix('inventory/api')->controller(InventorySubmoduleController::class)->group(function () {
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/state
    Route::get('/state', 'getState');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/alerts-summary
    Route::get('/alerts-summary', 'alertsSummary')->name('alerts.summary');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/inspection
    Route::post('/inspection', 'submitInspection');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/rma
    Route::post('/rma', 'submitRma');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/resolve-return
    Route::post('/resolve-return', 'resolveReturn');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/bundle
    Route::post('/bundle', 'submitBundle');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/resolve-bundle
    Route::post('/resolve-bundle', 'resolveBundle');

    // Alerts & Reorders submodule
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/limits/1
    Route::post('/limits/{id}', 'updateItemLimits');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/auto-reorder/1
    Route::post('/auto-reorder/{id}', 'toggleAutoReorder');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/submit-po
    Route::post('/submit-po', 'submitPO');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/draft/1/submit
    Route::post('/draft/{id}/submit', 'submitDraft');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/draft/1/discard
    Route::post('/draft/{id}/discard', 'discardDraft');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/pipeline/1
    Route::post('/pipeline/{id}', 'processPipeline');
    // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/pipeline/1/receive
    Route::post('/pipeline/{id}/receive', 'markReceived');
});

// Routes for stock movement submodule
// URL: http://inventory-dashboardreturnsqcbundling.test/movement
Route::get('/movement', [StockMovementController::class, 'index'])->name('stock-movements.index');

// URL: http://inventory-dashboardreturnsqcbundling.test/api/stock-movements/data
Route::get('/api/stock-movements/data', [StockMovementController::class, 'getDashboardData'])->name('stock-movements.data');
// URL: http://inventory-dashboardreturnsqcbundling.test/api/stock-movements
Route::post('/api/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
// URL: http://inventory-dashboardreturnsqcbundling.test/api/stock-movements/1/status
Route::patch('/api/stock-movements/{txId}/status', [StockMovementController::class, 'updateStatus'])->name('stock-movements.update-status');

// Routes for warehouse layouts submodule
// URL: http://inventory-dashboardreturnsqcbundling.test/warehouse
Route::get('/warehouse', [WarehouseLayoutController::class, 'index'])->name('warehouse.layout');

// Route for the standalone Alerts & Reorders page
// URL: http://inventory-dashboardreturnsqcbundling.test/alerts
Route::get('/alerts', [InventorySubmoduleController::class, 'alertsPage'])->name('inventory.alerts');

// Keep your AJAX data API endpoints mapped below it
Route::prefix('warehouse-layout')->group(function () {
    // URL: http://inventory-dashboardreturnsqcbundling.test/warehouse-layout/data
    Route::get('/data', [WarehouseLayoutController::class, 'getData'])->name('warehouse.layout.data');
    // URL: http://inventory-dashboardreturnsqcbundling.test/warehouse-layout/request
    Route::post('/request', [WarehouseLayoutController::class, 'storeRequest'])->name('warehouse.layout.request');
    // URL: http://inventory-dashboardreturnsqcbundling.test/warehouse-layout/batch-request
    Route::post('/batch-request', [WarehouseLayoutController::class, 'storeBatchRequest'])->name('warehouse.layout.batch-request');
    // URL: http://inventory-dashboardreturnsqcbundling.test/warehouse-layout/process/1
    Route::post('/process/{id}', [WarehouseLayoutController::class, 'processApproval'])->name('warehouse.layout.process');
});

// Routes for inventory items submodule
// URL: http://inventory-dashboardreturnsqcbundling.test/items
Route::get('/items', [InventoryController::class, 'index'])->name('inventory.index');

// API Storage Data Mutations
// URL: http://inventory-dashboardreturnsqcbundling.test/api/requests
Route::post('/api/requests', [InventoryController::class, 'storeRequest'])->name('api.requests.store');
// URL: http://inventory-dashboardreturnsqcbundling.test/api/requests/1/resolve
Route::post('/api/requests/{id}/resolve', [InventoryController::class, 'resolveRequest'])->name('api.requests.resolve');