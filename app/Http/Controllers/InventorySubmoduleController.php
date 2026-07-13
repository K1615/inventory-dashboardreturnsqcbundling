<?php

// app/Http/Controllers/InventorySubmoduleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{InventoryItem, SystemLog, QcInspection, RmaRequest, ReturnsAuditLog, BundleRequest};

class InventorySubmoduleController extends Controller
{
    public function index()
    {
        return view('inventory.submodule', ['initialData' => $this->getAppData()]);
    }

    public function getState()
    {
        return response()->json($this->getAppData());
    }

    private function getAppData()
    {
        return [
            'inventory' => InventoryItem::all(),
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
            ])
        ];
    }

    public function submitInspection(Request $request)
    {
        $part = InventoryItem::findOrFail($request->itemId);
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
        $part = InventoryItem::findOrFail($request->itemId);
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
                    InventoryItem::where('id', $req->itemId)->increment('stock');
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
                $outcome = "Approved: Returned to Mfg";
                $statusType = "neutral";
            } else {
                $outcome = "Voided by Manager";
                $statusType = "void";
            }
        }

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
            foreach ($req->recipe as $partId) {
                $part = InventoryItem::find($partId);
                if ($part && $part->stock > 0) {
                    $part->decrement('stock');
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
