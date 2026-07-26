<?php
// DESTINATION: inventory-dashboardreturnsqcbundling/routes/api.php
// (NEW FILE — if you don't have routes/api.php yet, create it with this content.
// Also remember to register it in bootstrap/app.php — see
// INVENTORY_bootstrap_app_ROUNDTRIP.php)

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReorderStatusController;

Route::post('/reorder-status', [ReorderStatusController::class, 'update']);
