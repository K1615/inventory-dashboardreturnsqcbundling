<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use App\Models\Item;
use App\Models\InventoryRequest;

class InventoryController extends Controller
{
    // Renders the main view dashboard workspace
    public function index()
    {
        $items = Item::all();
        $pending = InventoryRequest::where('outcome', 'PENDING')->orderBy('created_at', 'desc')->get();
        $history = InventoryRequest::whereIn('outcome', ['APPROVED', 'VOIDED'])->orderBy('updated_at', 'desc')->get();

        return view('inventory', compact('items', 'pending', 'history'));
    }

    // Handles submissions for adding, editing, or deleting items
    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:ADD,EDIT,DELETE',
            'requestor' => 'required|string',
            'target_item_id' => 'nullable|string', // <-- ALLOWS STRING IDs
            'proposed_data' => 'required|array'
        ]);

        $newRequest = InventoryRequest::create([
            'type' => $validated['type'],
            'requestor' => $validated['requestor'],
            'target_item_id' => $validated['target_item_id'] ?? null,
            'proposed_data' => $validated['proposed_data'],
            'outcome' => 'PENDING'
        ]);

        return response()->json(['success' => true, 'request' => $newRequest]);
    }

    // Resolves a review ticket with an Approval or Void verdict
    public function resolveRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approve,void',
            'reviewer' => 'required|string'
        ]);

        $invRequest = InventoryRequest::findOrFail($id);
        
        if ($invRequest->outcome !== 'PENDING') {
            return response()->json(['success' => false, 'message' => 'This record was already resolved.'], 400);
        }

        if ($validated['decision'] === 'approve') {
            // Safety check: ensure JSON data is cast to an array if the model doesn't do it automatically
            $data = is_string($invRequest->proposed_data) ? json_decode($invRequest->proposed_data, true) : $invRequest->proposed_data;

            if ($invRequest->type === 'ADD') {
                Item::create([
                    'id' => 'PRD-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'name' => $data['name'],
                    'category' => $data['category'],
                    'qty' => $data['qty'],
                    'price' => $data['price'],
                    'warehouse' => $data['warehouse'],
                    'zone' => $data['location'] ?? null, // Correctly mapped to the 'zone' column
                    'status' => $data['status'],
                    'desc' => $data['desc'] ?? null
                ]);
            } elseif ($invRequest->type === 'EDIT') {
                $item = Item::findOrFail($invRequest->target_item_id);
                $item->update([
                    'name' => $data['name'],
                    'category' => $data['category'],
                    'qty' => $data['qty'],
                    'price' => $data['price'],
                    'warehouse' => $data['warehouse'],
                    'zone' => $data['location'] ?? null, // Correctly mapped to the 'zone' column
                    'status' => $data['status'],
                    'desc' => $data['desc'] ?? null
                ]);
            } elseif ($invRequest->type === 'DELETE') {
                $item = Item::findOrFail($invRequest->target_item_id);
                $item->delete();
            }

            $invRequest->outcome = 'APPROVED';

            // Recalculate alerts now that an item was added/edited/removed —
            // otherwise an alert (e.g. Overstock) that's no longer true
            // after this change stays stuck until some other endpoint
            // happens to trigger a recheck.
            Artisan::call('stock:check-levels');
        } else {
            $invRequest->outcome = 'VOIDED';
        }

        $invRequest->reviewer = $validated['reviewer'];
        $invRequest->save();

        return response()->json(['success' => true]);
    }
}