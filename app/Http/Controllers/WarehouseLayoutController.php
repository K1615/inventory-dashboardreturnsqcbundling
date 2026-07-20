<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarehouseInventoryItem;
use App\Models\StockMovementRequest;
use App\Models\AuditMovementLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WarehouseLayoutController extends Controller
{
    public function index()
    {
        return view('warehouse_layout');
    }

    public function getData()
    {
        return response()->json([
            'inventory' => WarehouseInventoryItem::where('qty', '>', 0)->get(),
            'pendingRequests' => StockMovementRequest::all(),
            'historyLogs' => AuditMovementLog::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'itemId' => 'required|exists:warehouse_inventory_items,id',
            'requester' => 'required|string',
            'toWh' => 'required|string',
            'toZone' => 'required|string',
            'qty' => 'required|integer|min:1',
            'date' => 'required|date'
        ]);

        $item = WarehouseInventoryItem::findOrFail($validated['itemId']);

        if ($validated['qty'] > $item->qty) {
            return response()->json(['error' => 'Insufficient stock structural layouts.'], 422);
        }

        StockMovementRequest::create([
            'warehouse_inventory_item_id' => $item->id,
            'requester' => $validated['requester'],
            'name' => $item->name,
            'from_wh' => $item->warehouse,
            'from_zone' => $item->zone,
            'to_wh' => $validated['toWh'],
            'to_zone' => $validated['toZone'],
            'qty' => $validated['qty'],
            'planned_date' => $validated['date']
        ]);

        return response()->json(['success' => true]);
    }

    public function storeBatchRequest(Request $request)
    {
        $validated = $request->validate([
            'srcWh' => 'required|string',
            'targetWh' => 'required|string|different:srcWh',
            'date' => 'required|date',
            'requester' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:warehouse_inventory_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.toZone' => 'required|string'
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['items'] as $itemData) {
                $item = WarehouseInventoryItem::findOrFail($itemData['id']);
                if ($itemData['qty'] <= $item->qty) {
                    StockMovementRequest::create([
                        'warehouse_inventory_item_id' => $item->id,
                        'requester' => $validated['requester'],
                        'name' => $item->name,
                        'from_wh' => $validated['srcWh'],
                        'from_zone' => $item->zone,
                        'to_wh' => $validated['targetWh'],
                        'to_zone' => $itemData['toZone'],
                        'qty' => $itemData['qty'],
                        'planned_date' => $validated['date']
                    ]);
                }
            }
        });

        return response()->json(['success' => true]);
    }

    public function processApproval(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approve,void'
        ]);

        $pending = StockMovementRequest::findOrFail($id);
        $timestampStr = Carbon::now();

        DB::transaction(function () use ($pending, $validated, $timestampStr) {
            if ($validated['status'] === 'approve') {
                $sourceItem = WarehouseInventoryItem::findOrFail($pending->warehouse_inventory_item_id);
                
                if ($sourceItem->qty < $pending->qty) {
                    throw new \Exception("Insufficient stock structural layout allocation.");
                }

                $sourceItem->decrement('qty', $pending->qty);
                $sourceItem->update(['last_moved' => $timestampStr]);

                $targetItem = WarehouseInventoryItem::where('name', $pending->name)
                    ->where('warehouse', $pending->to_wh)
                    ->where('zone', $pending->to_zone)
                    ->first();

                if ($targetItem) {
                    $targetItem->increment('qty', $pending->qty);
                    $targetItem->update(['last_moved' => $timestampStr]);
                } else {
                    WarehouseInventoryItem::create([
                        'type' => $sourceItem->type,
                        'name' => $pending->name,
                        'warehouse' => $pending->to_wh,
                        'zone' => $pending->to_zone,
                        'qty' => $pending->qty,
                        'last_moved' => $timestampStr
                    ]);
                }

                AuditMovementLog::create([
                    'type' => 'APPROVED',
                    'name' => $pending->name,
                    'from_wh' => $pending->from_wh,
                    'to_wh' => $pending->to_wh,
                    'zone' => $pending->to_zone,
                    'qty' => $pending->qty,
                    'raw_date' => $pending->planned_date
                ]);
            } else {
                AuditMovementLog::create([
                    'type' => 'VOIDED',
                    'name' => $pending->name,
                    'from_wh' => $pending->from_wh,
                    'to_wh' => $pending->to_wh,
                    'zone' => $pending->to_zone,
                    'qty' => $pending->qty,
                    'raw_date' => $pending->planned_date
                ]);
            }

            $pending->delete();
        });

        return response()->json(['success' => true]);
    }
}