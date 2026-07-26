<?php
// DESTINATION: inventory-dashboardreturnsqcbundling/app/Services/AutoReorderService.php
// (REPLACE existing file with this — additions are the Http import and the
// try/catch block inside evaluate() that syncs the draft to Procurement)

namespace App\Services;

use App\Models\ApprovalRequest;
use App\Models\StockAlert;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AutoReorderService
{
    // Only these alert types are worth auto-drafting a reorder for.
    // Overstock alerts should never trigger a purchase order.
    protected const TRIGGER_TYPES = ['out_of_stock', 'low_stock'];

    // Any order in one of these statuses counts as "already handling this
    // item" — don't create a second draft on top of it.
    protected const IN_FLIGHT_STATUSES = ['Draft', 'Pending', 'Ordered'];

    /**
     * Scan active/acknowledged critical or low-stock alerts and, for any
     * item that has auto-reorder switched on, create a Draft purchase
     * order — provided one isn't already sitting in Draft/Pending/Ordered
     * for that item (so it doesn't spam a new draft every poll).
     *
     * Returns the ApprovalRequest rows it created, if any.
     */
    public static function evaluate(): array
    {
        $created = [];

        // CHANGED: 'inventoryItem' to 'item'
        $alerts = StockAlert::with('item')
            ->whereIn('status', ['active', 'acknowledged'])
            ->whereIn('type', self::TRIGGER_TYPES)
            ->get();

        // Track items already drafted for within this single call, in
        // addition to the DB check, since a single evaluate() run could
        // see both a low_stock and out_of_stock alert for the same item.
        $handledThisRun = [];

        foreach ($alerts as $alert) {
            // CHANGED: 'inventoryItem' to 'item'
            $item = $alert->item;

            if (!$item || !$item->auto_reorder) {
                continue;
            }

            if (in_array($item->id, $handledThisRun, true)) {
                continue;
            }

            if (self::hasInFlightOrder($item->id)) {
                $handledThisRun[] = $item->id;
                continue;
            }

            // CHANGED: $item->stock to $item->qty
            $qty = $item->reorder_qty ?? max(1, $item->maxLimit - $item->qty);

            $draft = ApprovalRequest::create([
                'timestamp' => now()->format('Y-m-d H:i'),
                'requester' => 'Auto-Reorder System',
                'details' => "Auto-generated draft: reorder {$qty}x {$item->name} ({$item->id}) — triggered by a {$alert->severity} {$alert->type} alert.",
                'supplier' => 'Global Logistics',
                'warehouse' => 'Alpha Warehouse',
                'status' => 'Draft',
                'source' => 'auto',
                'triggered_by_alert_id' => $alert->id,
            ]);

            $draft->items()->create([
                // CHANGED: 'inventory_item_id' to 'item_id'
                'item_id' => $item->id,
                'qty' => $qty,
            ]);

            SystemLog::create([
                'user' => 'Auto-Reorder System',
                'action' => "Auto-reorder triggered: created draft PO #{$draft->reqId} for {$item->name} ({$item->id}) — qty {$qty} — {$alert->severity} {$alert->type} alert.",
            ]);

            // Send this draft reorder to the Procurement system via API.
            try {
                $payload = [
                    'inventory_reorder_id' => (string) $draft->reqId,
                    'item_name'            => $item->name,
                    'qty'                  => $qty,
                    'supplier'             => $draft->supplier,
                    'priority'             => $alert->severity,
                    'justification'        => $draft->details,
                    'requestor'            => 'Inventory Auto-Reorder System',
                    'dept'                 => $draft->warehouse,
                ];

                // Logs the exact JSON payload to storage/logs/laravel.log
                // so it can be inspected/demoed.
                \Log::info('Sending to Procurement:', $payload);

                Http::withHeaders([
                    'X-API-Key' => config('services.procurement.key'),
                ])->post(config('services.procurement.url'), $payload);

                SystemLog::create([
                    'user' => 'Auto-Reorder System',
                    'action' => "Synced draft PO #{$draft->reqId} to Procurement.",
                ]);
            } catch (\Exception $e) {
                SystemLog::create([
                    'user' => 'Auto-Reorder System',
                    'action' => "Failed to sync draft PO #{$draft->reqId} to Procurement: " . $e->getMessage(),
                ]);
            }

            $handledThisRun[] = $item->id;
            
            // CHANGED: 'items.inventoryItem' to 'items.item'
            $created[] = $draft->load('items.item');
        }

        return $created;
    }

    /**
     * Direct DB query, bypassing Eloquent relationship resolution, so a
     * single run can never create two drafts for the same item.
     */
    // CHANGED: parameter from $inventoryItemId to $itemId for consistency
    protected static function hasInFlightOrder(string $itemId): bool
    {
        return DB::table('approval_requests')
            ->join('approval_request_items', 'approval_requests.reqId', '=', 'approval_request_items.approval_request_id')
            // CHANGED: 'approval_request_items.inventory_item_id' to 'approval_request_items.item_id'
            ->where('approval_request_items.item_id', $itemId)
            ->whereIn('approval_requests.status', self::IN_FLIGHT_STATUSES)
            ->exists();
    }
}