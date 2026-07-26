<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventorySubmoduleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WarehouseLayoutController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DemoLoginController;

// --- Demo login (session-based, no real auth) ---
Route::get('/login', [DemoLoginController::class, 'show'])->name('login');
Route::post('/login', [DemoLoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [DemoLoginController::class, 'logout'])->name('logout');

// Everything below requires a demo role to be selected first.
Route::middleware('role.selected')->group(function () {

    // Route the root URL directly to the Inventory Dashboard
    // URL: http://inventory-dashboardreturnsqcbundling.test/
    Route::get('/', [InventorySubmoduleController::class, 'index'])->name('inventory.dashboard');

    // Keep the API routes grouped for your fetch calls
    Route::prefix('inventory/api')->controller(InventorySubmoduleController::class)->group(function () {
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/state
        Route::get('/state', 'getState');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/alerts-summary
        Route::get('/alerts-summary', 'alertsSummary')->name('alerts.summary');

        // Returns / QC — every role can process a decision
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/inspection
        Route::post('/inspection', 'submitInspection');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/rma
        Route::post('/rma', 'submitRma');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/resolve-return
        Route::post('/resolve-return', 'resolveReturn');

        // Bundling — submitting a build/disassemble request is open to all roles
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/bundle
        Route::post('/bundle', 'submitBundle');
        // Approving/voiding a bundling request requires Manager or Admin
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/resolve-bundle
        Route::post('/resolve-bundle', 'resolveBundle')->middleware('permission:approve_void_bundle');

        // Alerts & Reorders submodule
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/limits/1
        Route::post('/limits/{id}', 'updateItemLimits')->middleware('permission:edit_limits');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/auto-reorder/1
        Route::post('/auto-reorder/{id}', 'toggleAutoReorder')->middleware('permission:edit_limits');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/submit-po
        Route::post('/submit-po', 'submitPO')->middleware('permission:create_po');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/draft/1/submit
        Route::post('/draft/{id}/submit', 'submitDraft')->middleware('permission:create_po');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/draft/1/discard
        Route::post('/draft/{id}/discard', 'discardDraft')->middleware('permission:discard_draft');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/pipeline/1
        Route::post('/pipeline/{id}', 'processPipeline')->middleware('permission:approve_void_pipeline');
        // URL: http://inventory-dashboardreturnsqcbundling.test/inventory/api/pipeline/1/receive
        // Marking as Received is open to every role.
        Route::post('/pipeline/{id}/receive', 'markReceived');
    });

    // Routes for stock movement submodule
    // URL: http://inventory-dashboardreturnsqcbundling.test/movement
    Route::get('/movement', [StockMovementController::class, 'index'])->name('stock-movements.index');

    // URL: http://inventory-dashboardreturnsqcbundling.test/api/stock-movements/data
    Route::get('/api/stock-movements/data', [StockMovementController::class, 'getDashboardData'])->name('stock-movements.data');
    // Submitting a new stock movement request is open to every role.
    // URL: http://inventory-dashboardreturnsqcbundling.test/api/stock-movements
    Route::post('/api/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
    // Approving/voiding a stock movement requires Manager or Admin.
    // URL: http://inventory-dashboardreturnsqcbundling.test/api/stock-movements/1/status
    Route::patch('/api/stock-movements/{txId}/status', [StockMovementController::class, 'updateStatus'])
        ->name('stock-movements.update-status')
        ->middleware('permission:approve_void_movement');

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
        // Submitting a move request is open to every role.
        // URL: http://inventory-dashboardreturnsqcbundling.test/warehouse-layout/request
        Route::post('/request', [WarehouseLayoutController::class, 'storeRequest'])->name('warehouse.layout.request');
        // URL: http://inventory-dashboardreturnsqcbundling.test/warehouse-layout/batch-request
        Route::post('/batch-request', [WarehouseLayoutController::class, 'storeBatchRequest'])->name('warehouse.layout.batch-request');
        // Approving/voiding a move requires Manager or Admin.
        // URL: http://inventory-dashboardreturnsqcbundling.test/warehouse-layout/process/1
        Route::post('/process/{id}', [WarehouseLayoutController::class, 'processApproval'])
            ->name('warehouse.layout.process')
            ->middleware('permission:approve_void_layout');
    });

    // Routes for inventory items submodule
    // URL: http://inventory-dashboardreturnsqcbundling.test/items
    Route::get('/items', [InventoryController::class, 'index'])->name('inventory.index');

    // API Storage Data Mutations
    // Submitting an ADD/EDIT/DELETE item request behaves like "Create PO / Submit-to-pipeline draft"
    // URL: http://inventory-dashboardreturnsqcbundling.test/api/requests
    Route::post('/api/requests', [InventoryController::class, 'storeRequest'])
        ->name('api.requests.store')
        ->middleware('permission:create_po');
    // Approving/voiding that request requires Manager or Admin.
    // URL: http://inventory-dashboardreturnsqcbundling.test/api/requests/1/resolve
    Route::post('/api/requests/{id}/resolve', [InventoryController::class, 'resolveRequest'])
        ->name('api.requests.resolve')
        ->middleware('permission:approve_void_pipeline');
});
