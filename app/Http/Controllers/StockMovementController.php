<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockMovement; // Your newly seeded model!

class StockMovementController extends Controller
{
    public function index()
    {
        // Simply return the view so the page loads without crashing
        // Note: Change 'stock-movements.index' if your folder structure is different
        return view('stock_movements'); 
    }

    // Replace the placeholder API route with this:
    public function getDashboardData() 
    { 
        // Fetch movements, ordered by newest first. 
        // If you have a relationship to the unified 'Item' model, load it here.
        $movements = StockMovement::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $movements
        ]); 
    }
}