<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\StockMovementRequest;
use App\Models\AuditMovementLog;
use App\Models\SystemLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class WarehouseLayoutController extends Controller
{
    public function index()
    {
        return view('warehouse_layout');
    }

    public function getData()
    {
        $inventory = Item::all();
        $warehouses = Item::distinct()->pluck('warehouse')->filter()->values();
        $zones = Item::distinct()->pluck('zone')->filter()->values();
        
        // 1. Fetch Pending Requests
        $requests = StockMovementRequest::with('item')
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->get();
        
        $pendingRequests = $requests->map(function ($req) {
            return [
                'id' => $req->id,
                'requester' => $req->requester,
                'name' => $req->item ? $req->item->name : 'Unknown Item',
                'from_wh' => $req->from_wh,
                'from_zone' => $req->from_zone,
                'to_wh' => $req->to_wh,
                'to_zone' => $req->to_zone,
                'qty' => $req->qty,
                'planned_date' => $req->planned_date,
            ];
        })->values()->toArray();

        // 2. Fetch Completed/Voided Requests for Audit Logs
        $logs = StockMovementRequest::with('item')
            ->whereRaw('LOWER(status) != ?', ['pending'])
            ->orderBy('updated_at', 'desc')
            ->take(50) // Limit to the 50 most recent logs so the UI doesn't lag over time
            ->get();
            
        $historyLogs = $logs->map(function ($log) {
            return [
                'type' => strtoupper($log->status), // Maps 'Approved' to 'APPROVED' for your frontend badge logic
                'qty' => $log->qty,
                'name' => $log->item ? $log->item->name : 'Unknown Item',
                'from_wh' => $log->from_wh,
                'to_wh' => $log->to_wh,
                'zone' => $log->to_zone,
                // Use the planned_date the user selected on the request (not
                // updated_at, which is just the moment it got approved/voided).
                // Fall back to updated_at only if planned_date is missing.
                'raw_date' => $log->planned_date
                    ? Carbon::parse($log->planned_date)->format('Y-m-d H:i:s')
                    : $log->updated_at->format('Y-m-d H:i:s'),
            ];
        })->values()->toArray();

        return response()->json([
            'inventory' => $inventory,
            'warehouses' => $warehouses,
            'zones' => $zones,
            'pendingRequests' => $pendingRequests, 
            'historyLogs' => $historyLogs // Replaced the hardcoded [] with the real database logs!
        ]);
    }

    // 1. Process Single Transfer Requests
    public function storeRequest(Request $request)
    {
        try {
            $item = Item::findOrFail($request->itemId);
            $actingUser = \App\Support\Roles::currentNameWithRole();

            StockMovementRequest::create([
                'item_id' => $item->id,
                'requester' => $actingUser,
                'from_wh' => $item->warehouse,
                'from_zone' => $item->zone,
                'to_wh' => $request->toWh,
                'to_zone' => $request->toZone,
                'qty' => $request->qty,
                'planned_date' => $request->date,
                'status' => 'Pending'
            ]);

            SystemLog::create([
                'user' => $actingUser,
                'action' => "Requested a transfer of {$request->qty}x {$item->name} from {$item->warehouse} to {$request->toWh}. Awaiting approval.",
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 2. Process Batch Transfer Requests
    public function storeBatchRequest(Request $request)
    {
        try {
            $actingUser = \App\Support\Roles::currentNameWithRole();
            foreach ($request->items as $reqItem) {
                $item = Item::findOrFail($reqItem['id']);
                
                StockMovementRequest::create([
                    'item_id' => $item->id,
                    'requester' => $actingUser,
                    'from_wh' => $request->srcWh,
                    'from_zone' => $item->zone,
                    'to_wh' => $request->targetWh,
                    'to_zone' => $reqItem['toZone'],
                    'qty' => $reqItem['qty'],
                    'planned_date' => $request->date,
                    'status' => 'Pending'
                ]);
            }

            SystemLog::create([
                'user' => $actingUser,
                'action' => "Requested a batch transfer of " . count($request->items) . " item(s) from {$request->srcWh} to {$request->targetWh}. Awaiting approval.",
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function processApproval(Request $request, $id)
    {
        try {
            $movementRequest = StockMovementRequest::findOrFail($id);

            // Guardrail: Lock the state machine to prevent double-clicking
            if (strtolower($movementRequest->status) !== 'pending') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Action Denied: This transaction has already been processed.'
                ], 400);
            }

            // 1. Process Manual Void
            if ($request->status === 'void') {
                $movementRequest->status = 'Voided';
                $movementRequest->save();

                $itemName = optional(Item::find($movementRequest->item_id))->name ?? $movementRequest->item_id;
                SystemLog::create([
                    'user' => \App\Support\Roles::currentNameWithRole(),
                    'action' => "Voided warehouse transfer request #{$movementRequest->id} for {$itemName}.",
                ]);

                return response()->json(['success' => true]);
            }

            // 2. Process Approval
            if ($request->status === 'approve') {
                $sourceItem = Item::findOrFail($movementRequest->item_id);

                // Auto-Void Guardrail: Prevent negative inventory if stock was used elsewhere while pending
                if ($sourceItem->qty < $movementRequest->qty) {
                    $movementRequest->status = 'Voided';
                    $movementRequest->save();

                    SystemLog::create([
                        'user' => 'System',
                        'action' => "Auto-voided warehouse transfer request #{$movementRequest->id} for {$sourceItem->name} — insufficient stock at source (requested {$movementRequest->qty}, had {$sourceItem->qty}).",
                    ]);

                    return response()->json([
                        'success' => false, 
                        'message' => 'Action Denied: Insufficient stock at source. Request has been automatically voided.'
                    ], 400);
                }

                // Step A: Deduct stock from the source location
                $sourceItem->qty -= $movementRequest->qty;
                $sourceItem->last_moved = now();
                $sourceItem->save();

                // Step B: Search for an exact match of this item in the destination warehouse/zone
                $targetItem = Item::where('name', $sourceItem->name)
                    ->where('warehouse', $movementRequest->to_wh)
                    ->where('zone', $movementRequest->to_zone)
                    ->first();

                if ($targetItem) {
                    // If the item exists in the target location, simply increase its quantity
                    $targetItem->qty += $movementRequest->qty;
                    $targetItem->last_moved = now();
                    $targetItem->save();
                } else {
                    // If the item is entirely new to this location, create a localized inventory record for it
                    Item::create([
                        'id' => 'PRD-' . strtoupper(Str::random(8)), // Matches your PRD-XXXXXXXX string format
                        'name' => $sourceItem->name,
                        'category' => $sourceItem->category,
                        'qty' => $movementRequest->qty,
                        'warehouse' => $movementRequest->to_wh,
                        'zone' => $movementRequest->to_zone,
                        'last_moved' => now()
                    ]);
                }

                // Step C: Mark the request as complete
                $movementRequest->status = 'Approved';
                $movementRequest->save();

                SystemLog::create([
                    'user' => \App\Support\Roles::currentNameWithRole(),
                    'action' => "Approved transfer of {$movementRequest->qty}x {$sourceItem->name} from {$movementRequest->from_wh} to {$movementRequest->to_wh}.",
                ]);

                // Step D: Recalculate alerts now that quantities at both the
                // source and destination have changed. Without this, an
                // alert (e.g. Overstock) that's no longer true after the
                // transfer stays stuck until some other endpoint happens to
                // trigger a recheck.
                Artisan::call('stock:check-levels');

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid action type requested.'], 400);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}