<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Part;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index()
    {
        // This servers the master UI template view layout wrapper
        return view('stock_movements');
    }

    public function getDashboardData()
    {
        $parts = Part::all();
        $movements = StockMovement::with('part')->orderBy('date', 'desc')->get();

        return response()->json([
            'parts' => $parts,
            'movements' => $movements
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tx_id' => 'required|unique:stock_movements,tx_id',
            'date' => 'required|date',
            'part_id' => 'required|exists:parts,id',
            'type' => 'required|string',
            'qty' => 'required|integer|min:1',
            'note' => 'required|string',
            'user' => 'required|string',
        ]);

        $movement = StockMovement::create(array_merge($validated, ['status' => 'Pending']));

        return response()->json([
            'success' => true,
            'message' => 'Transaction Log requested successfully!',
            'movement' => $movement->load('part')
        ]);
    }

    public function updateStatus(Request $request, $txId)
    {
        $request->validate([
            'status' => 'required|in:Approved,Voided'
        ]);

        $status = $request->status;

        return DB::transaction(function () use ($txId, $status) {
            $movement = StockMovement::findOrFail($txId);

            if ($movement->status !== 'Pending') {
                return response()->json(['success' => false, 'message' => 'Movement is already processed.'], 400);
            }

            if ($status === 'Approved') {
                $part = Part::findOrFail($movement->part_id);

                if (in_array($movement->type, ['Stock-Out', 'Warehouse Transfer'])) {
                    if ($part->stock < $movement->qty) {
                        return response()->json([
                            'success' => false, 
                            'message' => "Incompatible Action: Not enough items in stock. Current stock count: {$part->stock}"
                        ], 422);
                    }
                    $part->decrement('stock', $movement->qty);
                } else if (in_array($movement->type, ['Stock-In', 'Product Return'])) {
                    $part->increment('stock', $movement->qty);
                }
            }

            $movement->update([
                'status' => $status,
                'user' => 'Admin (Updated)'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Transaction status successfully updated to {$status}."
            ]);
        });
    }
}