<?php
// DESTINATION: inventory-dashboardreturnsqcbundling/app/Http/Controllers/ReorderStatusController.php
// (NEW FILE)

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ReorderStatusController extends Controller
{
    /**
     * Receives an approval/rejection status update from Procurement,
     * for a reorder that Inventory originally sent.
     */
    public function update(Request $request)
    {
        if ($request->header('X-API-Key') !== config('services.procurement.key')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'inventory_reorder_id' => 'required|string',
            'procurement_status'   => 'required|string|in:approved,rejected',
            'purchase_request_id'  => 'nullable|integer',
        ]);

        // Manual multi-item POs use "reqId-itemId" as the id; auto-reorder
        // drafts use just "reqId". Either way, the numeric reqId is the
        // part before the first dash (or the whole string if no dash).
        $reqId = explode('-', $validated['inventory_reorder_id'])[0];

        $draft = ApprovalRequest::where('reqId', $reqId)->first();

        if (!$draft) {
            return response()->json(['message' => 'No matching reorder found'], 404);
        }

        $draft->status = $validated['procurement_status'] === 'approved' ? 'Ordered' : 'Voided';
        $draft->save();

        SystemLog::create([
            'user' => 'Procurement System',
            'action' => "Procurement {$validated['procurement_status']} PO #{$draft->reqId} (Purchase Request #{$validated['purchase_request_id']}).",
        ]);

        return response()->json(['success' => true]);
    }
}
