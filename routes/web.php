<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventorySubmoduleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WarehouseLayoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', [InventorySubmoduleController::class, 'index'])->name('inventory.dashboard');

    Route::prefix('inventory/api')->controller(InventorySubmoduleController::class)->group(function () {
        Route::get('/state', 'getState');
        Route::get('/alerts-summary', 'alertsSummary')->name('alerts.summary');
        Route::post('/inspection', 'submitInspection');
        Route::post('/rma', 'submitRma');
        Route::post('/resolve-return', 'resolveReturn');
        Route::post('/bundle', 'submitBundle');
        Route::post('/resolve-bundle', 'resolveBundle');
        Route::post('/limits/{id}', 'updateItemLimits');
        Route::post('/auto-reorder/{id}', 'toggleAutoReorder');
        Route::post('/submit-po', 'submitPO');
        Route::post('/draft/{id}/submit', 'submitDraft');
        Route::post('/draft/{id}/discard', 'discardDraft');
        Route::post('/pipeline/{id}', 'processPipeline');
        Route::post('/pipeline/{id}/receive', 'markReceived');
    });

    Route::get('/movement', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('/api/stock-movements/data', [StockMovementController::class, 'getDashboardData'])->name('stock-movements.data');
    Route::post('/api/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
    Route::patch('/api/stock-movements/{txId}/status', [StockMovementController::class, 'updateStatus'])->name('stock-movements.update-status');

    Route::get('/warehouse', [WarehouseLayoutController::class, 'index'])->name('warehouse.layout');
    Route::get('/alerts', [InventorySubmoduleController::class, 'alertsPage'])->name('inventory.alerts');

    Route::prefix('warehouse-layout')->group(function () {
        Route::get('/data', [WarehouseLayoutController::class, 'getData'])->name('warehouse.layout.data');
        Route::post('/request', [WarehouseLayoutController::class, 'storeRequest'])->name('warehouse.layout.request');
        Route::post('/batch-request', [WarehouseLayoutController::class, 'storeBatchRequest'])->name('warehouse.layout.batch-request');
        Route::post('/process/{id}', [WarehouseLayoutController::class, 'processApproval'])->name('warehouse.layout.process');
    });

    Route::get('/items', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/api/requests', [InventoryController::class, 'storeRequest'])->name('api.requests.store');
    Route::post('/api/requests/{id}/resolve', [InventoryController::class, 'resolveRequest'])->name('api.requests.resolve');
});
