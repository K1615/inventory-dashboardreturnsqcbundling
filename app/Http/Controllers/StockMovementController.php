<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\StockMovement; // Your newly seeded model!
use App\Models\SystemLog;
use Illuminate\Support\Facades\Artisan;


class StockMovementController extends Controller
{
    public function index()
    {
        // Simply return the view so the page loads without crashing
        // Note: Change 'stock-movements.index' if your folder structure is different
        return view('stock_movements'); 
    }

    public function getDashboardData() 
    { 
        // 1. Fetch all inventory items
        $items = Item::all();
        
        // Map the items to match the frontend 'parts' expectation
        $parts = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'stock' => $item->qty, // Frontend expects 'stock' instead of 'qty'
                'exp_date' => null // Sending null as this isn't in your schema yet
            ];
        });

        // 2. Fetch all stock movements
        $dbMovements = StockMovement::orderBy('created_at', 'desc')->get();
        
        // Map the movements to match frontend variable expectations
        $movements = $dbMovements->map(function ($mov) {
            return [
                'tx_id' => $mov->tx_id,
                'date' => $mov->date,
                'part_id' => $mov->item_id, // Frontend expects 'part_id', DB uses 'item_id'
                'type' => $mov->type,
                'qty' => $mov->qty,
                'note' => $mov->note,
                'user' => $mov->user,
                'status' => $mov->status
            ];
        });
        
        // 3. Return the exact JSON structure the JavaScript fetch() is looking for
        return response()->json([
            'success' => true,
            'parts' => $parts,
            'movements' => $movements
        ]); 
    }

    public function store(Request $request)
    {
        try {
            // Create the new log in the database
            StockMovement::create([
                'tx_id'   => $request->tx_id,
                'date'    => $request->date,
                'item_id' => $request->part_id, // Map frontend 'part_id' to DB 'item_id'
                'type'    => $request->type,
                'qty'     => $request->qty,
                'note'    => $request->note,
                'user'    => $request->user,
                'status'  => 'Pending' // Explicitly set status so the DB doesn't crash
            ]);

            $item = Item::find($request->part_id);
            SystemLog::create([
                'user' => $request->user ?? 'Warehouse Manager',
                'action' => "Created a {$request->type} movement (#{$request->tx_id}) for " . ($item->name ?? $request->part_id) . " — qty {$request->qty}. Awaiting approval.",
            ]);

            // Return the success response the frontend fetch() expects
            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e) {
            // If something goes wrong, send the error message back
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $txId)
    {
        try {
            $movement = StockMovement::findOrFail($txId);
            $newStatus = $request->status;
            
            // STRICT GUARDRAIL: Lock the state machine. 
            // Reject ANY status updates if the request is already finalized.
            if ($movement->status !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Action Denied: This transaction has already been processed.'
                ]);
            }
            
            // If it's a fresh Pending request being approved, do the math
            $item = Item::find($movement->item_id);

            if ($newStatus === 'Approved') {
                if ($item) {
                    if ($movement->type === 'Stock-In' || $movement->type === 'Product Return') {
                        $item->qty += $movement->qty;
                    } 
                    elseif ($movement->type === 'Stock-Out') {
                        
                        // AUTO-VOID GUARDRAIL
                        if ($item->qty < $movement->qty) {
                            $movement->status = 'Voided';
                            $movement->note = $movement->note . ' (System Auto-Void: Insufficient stock)';
                            $movement->save();

                            SystemLog::create([
                                'user' => 'System',
                                'action' => "Auto-voided Stock-Out movement #{$movement->tx_id} for {$item->name} — insufficient stock (requested {$movement->qty}, had {$item->qty}).",
                            ]);

                            return response()->json([
                                'success' => false,
                                'message' => 'Action Denied: Insufficient stock. Request has been automatically voided.'
                            ]);
                        }
                        
                        $item->qty -= $movement->qty;
                    }
                    
                    $item->save();
                }
            }
            
            // Apply the requested status (Approved or manually Voided) and save
            $movement->status = $newStatus;
            $movement->save();

            $itemName = $item->name ?? $movement->item_id;
            if ($newStatus === 'Approved') {
                SystemLog::create([
                    'user' => 'Warehouse Manager',
                    'action' => "Approved {$movement->type} movement #{$movement->tx_id} for {$itemName} — qty {$movement->qty}. New stock: " . ($item->qty ?? 'n/a') . ".",
                ]);
            } else {
                SystemLog::create([
                    'user' => 'Warehouse Manager',
                    'action' => "Voided {$movement->type} movement #{$movement->tx_id} for {$itemName}.",
                ]);
            }

            // Recalculate alerts now that qty may have changed — otherwise
            // an alert that's no longer true after this movement (e.g. an
            // Overstock that a Stock-Out just fixed) stays stuck until some
            // other endpoint happens to trigger a recheck.
            if ($newStatus === 'Approved') {
                Artisan::call('stock:check-levels');
            }

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}