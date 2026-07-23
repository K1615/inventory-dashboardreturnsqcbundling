<?php

// app/Http/Controllers/InventorySubmoduleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Item, SystemLog, QcInspection, RmaRequest, ReturnsAuditLog, BundleRequest, StockAlert, ApprovalRequest, StockMovement, ShipmentHandoff};
use App\Services\AutoReorderService;
use Illuminate\Support\Facades\Artisan;

class InventorySubmoduleController extends Controller
{
    private const ACTING_USER = 'Warehouse Manager';

    public function index()
    {
        return view('inventory.submodule', ['initialData' => $this->getAppData()]);
    }

    public function getState()
    {
        return response()->json($this->getAppData());
    }

    // Standalone Alerts & Reorders page (separated from the tabbed dashboard SPA).
    // Reuses the same getAppData() payload as the dashboard tab and the mutation
    // endpoints below, so this page and the dashboard tab never disagree on data
    // shape — only the view differs.
    public function alertsPage()
    {
        return view('inventory.alerts-page', ['initialData' => $this->getAppData()]);
    }

    // Lightweight endpoint for the nav badge on pages that don't already
    // load full inventory data (Inventory Items, Stock Movements,
    // Warehouse Layout). Computed live from qty vs minLimit/maxLimit —
    // same logic as the Alerts & Reorders and Dashboard widgets — so it
    // never disagrees with what those pages show.
    public function alertsSummary()
    {
        $low = 0; $out = 0;
        Item::all()->each(function ($item) use (&$low, &$out) {
            $qty = (int) $item->qty;
            $min = (int) $item->minLimit;
            if ($qty === 0) $out++;
            elseif ($min > 0 && $qty < $min) $low++;
        });

        return response()->json([
            'count' => $low + $out,
            'hasCritical' => $out > 0,
        ]);
    }

    private function getAppData()
    {
        return [
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
        ];
    }

    // ================= Alerts & Reorders submodule =================

    public function updateItemLimits(Request $request, $id)
    {
        $request->validate([
            'target' => 'required|in:min,max',
            'value' => 'required|integer|min:0'
        ]);

        $item = Item::findOrFail($id);
        $oldValue = $request->target === 'min' ? $item->minLimit : $item->maxLimit;

        if ($request->target === 'min') {
            $item->minLimit = $request->value;
        } else {
            $item->maxLimit = $request->value;
        }
        $item->save();

        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Changed {$request->target} limit for {$item->name} ({$item->id}) from {$oldValue} to {$request->value}."]);

        Artisan::call('stock:check-levels');

        return response()->json($this->getAppData());
    }

    public function toggleAutoReorder(Request $request, $id)
    {
        $request->validate(['enabled' => 'required|boolean']);

        $item = Item::findOrFail($id);
        $item->auto_reorder = $request->boolean('enabled');
        $item->save();

        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Turned auto-reorder " . ($item->auto_reorder ? 'ON' : 'OFF') . " for {$item->name} ({$item->id})."]);

        Artisan::call('stock:check-levels');

        return response()->json($this->getAppData());
    }

    public function acknowledgeAlert($id)
    {
        $alert = StockAlert::findOrFail($id);
        $alert->update([
            'status' => 'acknowledged',
            'acknowledged_by' => self::ACTING_USER,
            'acknowledged_at' => now(),
        ]);

        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Acknowledged the {$alert->severity} {$alert->type} alert for {$alert->item_id}."]);

        return response()->json($this->getAppData());
    }

    public function resolveAlert($id)
    {
        $alert = StockAlert::findOrFail($id);
        $alert->update(['status' => 'resolved', 'resolved_at' => now()]);

        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Manually resolved the {$alert->severity} {$alert->type} alert for {$alert->item_id}."]);

        return response()->json($this->getAppData());
    }

    public function submitPO(Request $request)
    {
        $request->validate([
            'details' => 'required|string',
            'supplier' => 'required|string',
            'warehouse' => 'required|string',
            'itemsArray' => 'required|array',
        ]);

        $newRequest = ApprovalRequest::create([
            'timestamp' => now()->format('Y-m-d H:i'),
            'requester' => self::ACTING_USER,
            'details' => $request->details,
            'supplier' => $request->supplier,
            'warehouse' => $request->warehouse,
            'status' => 'Pending',
            'source' => 'manual',
        ]);

        foreach ($request->itemsArray as $item) {
            if (!isset($item['id'], $item['qty'])) continue;
            $newRequest->items()->create([
                'item_id' => $item['id'],
                'qty' => $item['qty'],
            ]);
        }

        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Submitted a new purchase order #{$newRequest->reqId} — {$request->details}."]);

        // ADD THIS LINE HERE
        Artisan::call('stock:check-levels');

        return response()->json($this->getAppData());
    }

    public function submitDraft($id)
    {
        $pipeline = ApprovalRequest::findOrFail($id);
        if ($pipeline->status !== 'Draft') {
            return response()->json(['success' => false, 'message' => 'Only a Draft order can be submitted to the pipeline.'], 400);
        }

        $pipeline->status = 'Pending';
        $pipeline->save();

        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Reviewed and submitted auto-generated draft PO #{$pipeline->reqId} into the approval pipeline."]);

        // ADD THIS LINE HERE
        Artisan::call('stock:check-levels');

        return response()->json($this->getAppData());
    }

    public function discardDraft($id)
    {
        $pipeline = ApprovalRequest::findOrFail($id);
        if ($pipeline->status !== 'Draft') {
            return response()->json(['success' => false, 'message' => 'Only a Draft order can be discarded.'], 400);
        }

        $pipeline->status = 'Voided';
        $pipeline->save();

        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Discarded auto-generated draft PO #{$pipeline->reqId}."]);

        $autoReorderTurnedOff = false;
        if ($pipeline->source === 'auto') {
            $itemIds = $pipeline->items->pluck('item_id')->unique();
            foreach ($itemIds as $itemId) {
                $item = Item::find($itemId);
                if ($item && $item->auto_reorder) {
                    $item->auto_reorder = false;
                    $item->save();
                    $autoReorderTurnedOff = true;

                    SystemLog::create(['user' => self::ACTING_USER, 'action' => "Turned auto-reorder OFF for {$item->name} ({$item->id}) — its auto-generated draft PO #{$pipeline->reqId} was discarded."]);
                }
            }
        }

        $data = $this->getAppData();
        $data['autoReorderTurnedOff'] = $autoReorderTurnedOff;

        // ADD THIS LINE HERE
        Artisan::call('stock:check-levels');
        
        return response()->json($data);
    }

    public function processPipeline(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Approved,Voided']);

        $pipeline = ApprovalRequest::findOrFail($id);
        if ($pipeline->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'Order is already processed.'], 400);
        }

        if ($request->status === 'Voided') {
            $pipeline->status = 'Voided';
            $pipeline->save();
            SystemLog::create(['user' => self::ACTING_USER, 'action' => "Voided purchase order #{$pipeline->reqId}."]);
            return response()->json($this->getAppData());
        }

        $pipeline->status = 'Ordered';
        $pipeline->save();
        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Approved purchase order #{$pipeline->reqId} — order placed with {$pipeline->supplier}. Awaiting delivery."]);

        // ADD THIS LINE HERE
        Artisan::call('stock:check-levels');

        return response()->json($this->getAppData());
    }

    public function markReceived($id)
    {
        $pipeline = ApprovalRequest::findOrFail($id);
        if ($pipeline->status !== 'Ordered') {
            return response()->json(['success' => false, 'message' => 'Only an Ordered request can be marked as Received.'], 400);
        }

        foreach ($pipeline->items as $lineItem) {
            // 1. Log the receipt in the correct table
            ShipmentHandoff::create([
                'item_id' => $lineItem->item_id,
                'type' => 'receipt',
                'qty' => $lineItem->qty,
                'source_type' => 'purchase_order',
                'source_id' => $pipeline->reqId,
                'created_by' => self::ACTING_USER,
            ]);

            // 2. Actually add the quantity to the master inventory
            $item = \App\Models\Item::find($lineItem->item_id);
            if ($item) {
                // Incrementing the 'qty' field as established by your schema
                $item->increment('qty', $lineItem->qty);
            }
        }

        $pipeline->status = 'Received';
        $pipeline->save();

        // 3. Recalculate alerts now that the stock has actually increased
        Artisan::call('stock:check-levels');

        SystemLog::create(['user' => self::ACTING_USER, 'action' => "Marked purchase order #{$pipeline->reqId} as Received — stock updated and recorded in Shipment Handoffs."]);

        return response()->json($this->getAppData());
    }

    public function submitInspection(Request $request)
    {
        $part = Item::findOrFail($request->itemId);
        QcInspection::create([
            'id' => 'REQ-I-' . rand(1000, 9999),
            'op' => $request->op,
            'itemId' => $part->id,
            'product' => $part->name,
            'source' => $request->source,
            'action' => $request->outcome
        ]);
        return response()->json($this->getAppData());
    }

    public function submitRma(Request $request)
    {
        $part = Item::findOrFail($request->itemId);
        RmaRequest::create([
            'id' => 'REQ-R-' . rand(1000, 9999),
            'op' => $request->op,
            'itemId' => $part->id,
            'product' => $part->name,
            'vendor' => $request->vendor,
            'reasons' => $request->reasons
        ]);
        return response()->json($this->getAppData());
    }

    public function resolveReturn(Request $request)
    {
        $type = $request->type;
        $decision = $request->decision;
        
        if ($type === 'Inspection') {
            $req = QcInspection::findOrFail($request->id);
            $req->status = $decision;
            $req->save();
            $infoStr = "{$req->product} (Source: {$req->source})";
            
            if ($decision === 'Approved') {
                if (str_contains($req->action, 'Restock') || str_contains($req->action, 'Open Box')) {
                    Item::where('id', $req->itemId)->increment('qty');
                    $outcome = "Approved: Restocked (+1)";
                    $statusType = "success";
                } else {
                    $outcome = "Approved: Quarantined";
                    $statusType = "danger";
                }
            } else {
                $outcome = "Voided by Manager";
                $statusType = "void";
            }
        } else {
            $req = RmaRequest::findOrFail($request->id);
            $req->status = $decision;
            $req->save();
            $infoStr = "{$req->product} (Vendor: {$req->vendor} - Reason: {$req->reasons})";
            
            if ($decision === 'Approved') {
                Item::where('id', $req->itemId)->decrement('qty');
                $outcome = "Approved: Returned to Mfg (-1)";
                $statusType = "neutral";
            } else {
                $outcome = "Voided by Manager";
                $statusType = "void";
            }
        }

        // Make sure there is NO closing brace '}' right above this line!
        ReturnsAuditLog::create([
            'op' => $req->op, 'stream' => $type, 'info' => $infoStr, 'outcome' => $outcome, 'statusType' => $statusType
        ]);
        
        SystemLog::create(['user' => $req->op, 'action' => "Resolved QC {$type}: {$outcome} for {$req->product}"]);

        return response()->json($this->getAppData());
    }

    public function submitBundle(Request $request)
    {
        BundleRequest::create([
            'id' => 'REQ-B-' . rand(1000, 9999),
            'requester' => $request->requester,
            'type' => $request->type,
            'details' => $request->details,
            'recipe' => $request->recipe
        ]);
        return response()->json($this->getAppData());
    }

    public function resolveBundle(Request $request)
    {
        $req = BundleRequest::findOrFail($request->id);
        $decision = $request->decision;
        $approver = $request->approver;

        if ($decision === 'Approved') {
            $shortages = [];
            foreach ($req->recipe as $partId) {
                $part = Item::find($partId);
                // FIX: Look at 'qty', not 'stock'
                if (!$part || $part->qty <= 0) {
                    $shortages[] = $part ? $part->name : $partId;
                }
            }

            if (!empty($shortages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot approve — out of stock: ' . implode(', ', $shortages) . '.',
                ], 400);
            }

            foreach ($req->recipe as $partId) {
                $part = Item::find($partId);
                if ($part && $part->qty > 0) {
                    // FIX: Decrement the 'qty' column
                    $part->decrement('qty');
                }
            }
        }

        $req->status = $decision;
        $req->approver = $approver;
        $req->save();

        SystemLog::create(['user' => $approver, 'action' => "Resolved Assembly Req: {$decision} for {$req->type}"]);

        return response()->json($this->getAppData());
    }
}