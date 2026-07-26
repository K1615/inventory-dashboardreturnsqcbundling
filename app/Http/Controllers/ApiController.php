<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{Item, SystemLog, QcInspection, RmaRequest, ReturnsAuditLog, BundleRequest, StockAlert, ApprovalRequest, StockMovement, StockMovementRequest};

class ApiController extends Controller
{
    // Replaces InventorySubmoduleController@getState[cite: 3]
    public function getInventoryState()
    {
        return response()->json([
            'inventory' => Item::all(),
            'systemLogs' => SystemLog::orderBy('created_at', 'desc')->take(50)->get()->map(fn($l) => [
                'user' => $l->user, 'action' => $l->action, 'timestamp' => $l->created_at->format('Y-m-d H:i:s')
            ]),
            'inspections' => QcInspection::where('status', 'Pending')->orderBy('created_at', 'desc')->get()->map(fn($i) => [
                'id' => $i->id, 'op' => $i->op, 'itemId' => $i->itemId, 'product' => $i->product, 'source' => $i->source, 'action' => $i->action, 'date' => $i->created_at->format('Y-m-d H:i:s')
            ]),
            'rmas' => RmaRequest::where('status', 'Pending')->orderBy('created_at', 'desc')->get()->map(fn($r) => [
                'id' => $r->id, 'op' => $r->op, 'itemId' => $r->itemId, 'product' => $r->product, 'vendor' => $r->vendor, 'reasons' => $r->reasons, 'date' => $r->created_at->format('Y-m-d H:i:s')
            ]),
            'returnsAudit' => ReturnsAuditLog::orderBy('created_at', 'desc')->get()->map(fn($a) => [
                'op' => $a->op, 'stream' => $a->stream, 'info' => $a->info, 'outcome' => $a->outcome, 'statusType' => $a->statusType, 'time' => $a->created_at->format('Y-m-d H:i:s')
            ]),
            'bundlePending' => BundleRequest::where('status', 'Pending')->orderBy('created_at', 'desc')->get()->map(fn($b) => [
                'id' => $b->id, 'requester' => $b->requester, 'type' => $b->type, 'details' => $b->details, 'recipe' => $b->recipe, 'requestDate' => $b->created_at->format('Y-m-d H:i:s')
            ]),
            'bundleAudit' => BundleRequest::where('status', '!=', 'Pending')->orderBy('updated_at', 'desc')->get()->map(fn($b) => [
                'id' => $b->id, 'requester' => $b->requester, 'approver' => $b->approver, 'type' => $b->type, 'details' => $b->details, 'status' => $b->status, 'actionDate' => $b->updated_at->format('Y-m-d H:i:s')
            ]),
            'stockAlerts' => StockAlert::with('item')->whereIn('status', ['active', 'acknowledged'])->latest()->get(),
            'approvalRequests' => ApprovalRequest::with('items.item')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    // Replaces InventorySubmoduleController@alertsSummary[cite: 3]
    public function getAlertsSummary()
    {
        $activeAlerts = StockAlert::where('status', 'active')->get();

        return response()->json([
            'count' => $activeAlerts->count(),
            'hasCritical' => $activeAlerts->contains('type', 'out_of_stock'),
        ]);
    }

    // Replaces StockMovementController@getDashboardData[cite: 4]
    public function getStockMovements()
    {
        $items = Item::all();
        
        $parts = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'stock' => $item->qty, 
                'exp_date' => null 
            ];
        });

        $dbMovements = StockMovement::orderBy('created_at', 'desc')->get();
        
        $movements = $dbMovements->map(function ($mov) {
            return [
                'tx_id' => $mov->tx_id,
                'date' => $mov->date,
                'part_id' => $mov->item_id, 
                'type' => $mov->type,
                'qty' => $mov->qty,
                'note' => $mov->note,
                'user' => $mov->user,
                'status' => $mov->status
            ];
        });
        
        return response()->json([
            'success' => true,
            'parts' => $parts,
            'movements' => $movements
        ]); 
    }

    // Replaces WarehouseLayoutController@getData[cite: 5]
    public function getWarehouseLayoutData()
    {
        $inventory = Item::all();
        $warehouses = Item::distinct()->pluck('warehouse')->filter()->values();
        $zones = Item::distinct()->pluck('zone')->filter()->values();
        
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

        $logs = StockMovementRequest::with('item')
            ->whereRaw('LOWER(status) != ?', ['pending'])
            ->orderBy('updated_at', 'desc')
            ->take(50) 
            ->get();
            
        $historyLogs = $logs->map(function ($log) {
            return [
                'type' => strtoupper($log->status), 
                'qty' => $log->qty,
                'name' => $log->item ? $log->item->name : 'Unknown Item',
                'from_wh' => $log->from_wh,
                'to_wh' => $log->to_wh,
                'zone' => $log->to_zone,
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
            'historyLogs' => $historyLogs 
        ]);
    }

    /**
     * Simulates the dynamic JSON data output sent TO other ERP modules.
     * Fetches live data but restricts the payload to specific columns.
     */
    public function getErpOutput()
    {
        // Assuming your Eloquent model is named 'Item'. 
        // The select() method ensures only these specific columns are pulled and outputted.
        $erpData = \App\Models\Item::select(
            'id', 
            'name', 
            'category', 
            'qty', 
            'warehouse', 
            'zone', 
            'price'
        )->get();

        return response()->json($erpData);
    }

    /**
     * Simulates the JSON data input received FROM another ERP module.
     */
    public function getErpInput()
    {
        return response()->json([
            [
                'id' => 'EXT-8831',
                'name' => 'Bulk Thermal Paste',
                'category' => 'Consumables',
                'qty' => 100,
                'price' => 12.50
            ],
            [
                'id' => 'EXT-8832',
                'name' => 'Replacement Fans 120mm',
                'category' => 'Cooling',
                'qty' => 50,
                'price' => 24.00
            ]
        ]);
    }
}