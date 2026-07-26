<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->controller(ApiController::class)->group(function () {
    
    // Core Dashboard & Alerts
    
    // URL: GET http://inventory-dashboardreturnsqcbundling.test/api/v1/inventory/state
    Route::get('/inventory/state', 'getInventoryState');

    // URL: GET http://inventory-dashboardreturnsqcbundling.test/api/v1/inventory/alerts-summary
    Route::get('/inventory/alerts-summary', 'getAlertsSummary');
    
    // Submodule Data Payloads

    // URL: GET http://inventory-dashboardreturnsqcbundling.test/api/v1/stock-movements/data
    Route::get('/stock-movements/data', 'getStockMovements');

    // URL: GET http://inventory-dashboardreturnsqcbundling.test/api/v1/warehouse-layout/data
    Route::get('/warehouse-layout/data', 'getWarehouseLayoutData');

    // ERP Simulation Routes
    
    // GET API for output data for other ERP module
    // URL: GET http://inventory-dashboardreturnsqcbundling.test/api/v1/erp/output
    Route::get('/erp/output', 'getErpOutput');

    // Example view list of the Data we would receive from other submodule
    // URL: GET http://inventory-dashboardreturnsqcbundling.test/api/v1/erp/input
    Route::get('/erp/input', 'getErpInput');
    
});

