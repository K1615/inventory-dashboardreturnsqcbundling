<?php

namespace Tests\Feature;

use App\Models\ApprovalRequest;
use App\Models\BundleRequest;
use App\Models\InventoryRequest;
use App\Models\Item;
use App\Models\QcInspection;
use App\Models\RmaRequest;
use App\Models\StockMovement;
use App\Models\StockMovementRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErpModuleRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['name' => 'Regression Operator']));
    }

    public function test_dashboard_pages_and_state_endpoints_load_while_authenticated(): void
    {
        foreach (['/', '/alerts', '/items', '/movement', '/warehouse'] as $uri) {
            $this->get($uri)->assertOk();
        }

        $this->getJson('/inventory/api/state')
            ->assertOk()
            ->assertJsonStructure([
                'inventory',
                'systemLogs',
                'inspections',
                'rmas',
                'returnsAudit',
                'bundlePending',
                'bundleAudit',
                'stockAlerts',
                'approvalRequests',
            ]);

        $this->getJson('/inventory/api/alerts-summary')
            ->assertOk()
            ->assertJsonStructure(['count', 'hasCritical']);

        $this->getJson('/api/stock-movements/data')
            ->assertOk()
            ->assertJsonStructure(['success', 'parts', 'movements']);

        $this->getJson('/warehouse-layout/data')
            ->assertOk()
            ->assertJsonStructure([
                'inventory',
                'warehouses',
                'zones',
                'pendingRequests',
                'historyLogs',
            ]);
    }

    public function test_returns_and_qc_approval_void_and_quantity_rules_still_work(): void
    {
        $item = $this->createItem(['qty' => 5]);

        $this->postJson('/inventory/api/inspection', [
            'itemId' => $item->id,
            'source' => 'Customer Aftersales',
            'outcome' => 'Good - Clear for Restock',
        ])->assertOk();

        $inspection = QcInspection::sole();
        $this->postJson('/inventory/api/resolve-return', [
            'type' => 'Inspection',
            'id' => $inspection->id,
            'decision' => 'Approved',
        ])->assertOk();

        $this->assertSame(6, $item->fresh()->qty);

        $this->postJson('/inventory/api/rma', [
            'itemId' => $item->id,
            'vendor' => 'Manufacturer',
            'reasons' => 'Physical Defect',
        ])->assertOk();

        $rma = RmaRequest::sole();
        $this->postJson('/inventory/api/resolve-return', [
            'type' => 'RMA',
            'id' => $rma->id,
            'decision' => 'Approved',
        ])->assertOk();

        $this->assertSame(5, $item->fresh()->qty);

        $voidedRma = RmaRequest::create([
            'id' => 'REQ-R-VOID',
            'op' => 'Regression Operator',
            'itemId' => $item->id,
            'product' => $item->name,
            'vendor' => 'Manufacturer',
            'reasons' => 'Packaging review',
        ]);

        $this->postJson('/inventory/api/resolve-return', [
            'type' => 'RMA',
            'id' => $voidedRma->id,
            'decision' => 'Voided',
        ])->assertOk();

        $this->assertSame(5, $item->fresh()->qty);
    }

    public function test_bundling_approval_void_and_insufficient_stock_rules_still_work(): void
    {
        $item = $this->createItem(['qty' => 2]);

        $this->postJson('/inventory/api/bundle', [
            'type' => 'Custom Build',
            'details' => 'Approved build',
            'recipe' => [$item->id],
        ])->assertOk();

        $approved = BundleRequest::sole();
        $this->postJson('/inventory/api/resolve-bundle', [
            'id' => $approved->id,
            'decision' => 'Approved',
        ])->assertOk();

        $this->assertSame(1, $item->fresh()->qty);

        $this->postJson('/inventory/api/bundle', [
            'type' => 'Pre-built',
            'details' => 'Voided build',
            'recipe' => [$item->id],
        ])->assertOk();

        $voided = BundleRequest::where('status', 'Pending')->sole();
        $this->postJson('/inventory/api/resolve-bundle', [
            'id' => $voided->id,
            'decision' => 'Voided',
        ])->assertOk();

        $this->assertSame(1, $item->fresh()->qty);

        $item->update(['qty' => 0]);
        $this->postJson('/inventory/api/bundle', [
            'type' => 'Custom Build',
            'details' => 'Blocked build',
            'recipe' => [$item->id],
        ])->assertOk();

        $blocked = BundleRequest::where('status', 'Pending')->sole();
        $this->postJson('/inventory/api/resolve-bundle', [
            'id' => $blocked->id,
            'decision' => 'Approved',
        ])->assertStatus(400);

        $this->assertSame('Pending', $blocked->fresh()->status);
    }

    public function test_alert_and_purchase_order_workflows_still_work(): void
    {
        $item = $this->createItem(['qty' => 10, 'maxLimit' => 100]);

        $this->postJson("/inventory/api/limits/{$item->id}", [
            'target' => 'min',
            'value' => 1,
        ])->assertOk();

        $this->postJson("/inventory/api/auto-reorder/{$item->id}", [
            'enabled' => true,
        ])->assertOk();

        $this->assertSame(1, $item->fresh()->minLimit);
        $this->assertTrue((bool) $item->fresh()->auto_reorder);

        $this->postJson('/inventory/api/submit-po', [
            'details' => 'Manual regression order',
            'supplier' => 'Regression Supplier',
            'warehouse' => 'Warehouse A',
            'itemsArray' => [
                ['id' => $item->id, 'qty' => 4],
            ],
        ])->assertOk();

        $manual = ApprovalRequest::where('source', 'manual')->sole();
        $this->postJson("/inventory/api/pipeline/{$manual->reqId}", [
            'status' => 'Approved',
        ])->assertOk();
        $this->assertSame('Ordered', $manual->fresh()->status);

        $this->postJson("/inventory/api/pipeline/{$manual->reqId}/receive")
            ->assertOk();
        $this->assertSame('Received', $manual->fresh()->status);
        $this->assertSame(14, $item->fresh()->qty);

        $submittedDraft = $this->createPurchaseOrder($item, 'Draft', 'auto');
        $this->postJson("/inventory/api/draft/{$submittedDraft->reqId}/submit")
            ->assertOk();
        $this->assertSame('Pending', $submittedDraft->fresh()->status);

        $discardedDraft = $this->createPurchaseOrder($item, 'Draft', 'auto');
        $this->postJson("/inventory/api/draft/{$discardedDraft->reqId}/discard")
            ->assertOk();
        $this->assertSame('Voided', $discardedDraft->fresh()->status);
        $this->assertFalse((bool) $item->fresh()->auto_reorder);

        $voidedOrder = $this->createPurchaseOrder($item, 'Pending', 'manual');
        $this->postJson("/inventory/api/pipeline/{$voidedOrder->reqId}", [
            'status' => 'Voided',
        ])->assertOk();
        $this->assertSame('Voided', $voidedOrder->fresh()->status);
    }

    public function test_stock_movement_approval_void_and_auto_void_rules_still_work(): void
    {
        $item = $this->createItem(['qty' => 5]);

        $this->createMovementThroughEndpoint($item, 'TX-REG-001', 2);
        $this->patchJson('/api/stock-movements/TX-REG-001/status', [
            'status' => 'Approved',
        ])->assertOk();
        $this->assertSame(3, $item->fresh()->qty);

        $this->createMovementThroughEndpoint($item, 'TX-REG-002', 1);
        $this->patchJson('/api/stock-movements/TX-REG-002/status', [
            'status' => 'Voided',
        ])->assertOk();
        $this->assertSame(3, $item->fresh()->qty);

        $this->createMovementThroughEndpoint($item, 'TX-REG-003', 99);
        $this->patchJson('/api/stock-movements/TX-REG-003/status', [
            'status' => 'Approved',
        ])->assertOk()->assertJson(['success' => false]);

        $this->assertSame('Voided', StockMovement::find('TX-REG-003')->status);
        $this->assertSame(3, $item->fresh()->qty);
    }

    public function test_warehouse_single_batch_and_insufficient_stock_rules_still_work(): void
    {
        $source = $this->createItem(['qty' => 5]);

        $this->postJson('/warehouse-layout/request', [
            'itemId' => $source->id,
            'toWh' => 'Warehouse B',
            'toZone' => 'Zone B',
            'qty' => 2,
            'date' => '2026-07-26',
        ])->assertOk();

        $single = StockMovementRequest::sole();
        $this->postJson("/warehouse-layout/process/{$single->id}", [
            'status' => 'approve',
        ])->assertOk();

        $this->assertSame(3, $source->fresh()->qty);
        $this->assertDatabaseHas('items', [
            'name' => $source->name,
            'warehouse' => 'Warehouse B',
            'zone' => 'Zone B',
            'qty' => 2,
        ]);

        $batchSource = $this->createItem([
            'id' => 'REG-ITEM-002',
            'name' => 'Batch Transfer Item',
            'qty' => 4,
        ]);

        $this->postJson('/warehouse-layout/batch-request', [
            'srcWh' => 'Warehouse A',
            'targetWh' => 'Warehouse C',
            'date' => '2026-07-27',
            'items' => [
                ['id' => $batchSource->id, 'toZone' => 'Zone C', 'qty' => 2],
            ],
        ])->assertOk();

        $batch = StockMovementRequest::where('item_id', $batchSource->id)->sole();
        $this->postJson("/warehouse-layout/process/{$batch->id}", [
            'status' => 'void',
        ])->assertOk();
        $this->assertSame(4, $batchSource->fresh()->qty);

        $this->postJson('/warehouse-layout/request', [
            'itemId' => $batchSource->id,
            'toWh' => 'Warehouse B',
            'toZone' => 'Zone D',
            'qty' => 99,
            'date' => '2026-07-28',
        ])->assertOk();

        $insufficient = StockMovementRequest::where('status', 'Pending')->sole();
        $this->postJson("/warehouse-layout/process/{$insufficient->id}", [
            'status' => 'approve',
        ])->assertStatus(400);

        $this->assertSame('Voided', $insufficient->fresh()->status);
        $this->assertSame(4, $batchSource->fresh()->qty);
    }

    public function test_inventory_add_edit_delete_request_rules_still_work(): void
    {
        $this->postJson('/api/requests', [
            'type' => 'ADD',
            'proposed_data' => [
                'name' => 'New Inventory Item',
                'category' => 'Test',
                'qty' => 7,
                'price' => 12.50,
                'warehouse' => 'Warehouse A',
                'location' => 'Zone A',
                'status' => 'Active',
                'desc' => 'Added through approval',
            ],
        ])->assertOk();

        $add = InventoryRequest::sole();
        $this->postJson("/api/requests/{$add->id}/resolve", [
            'decision' => 'approve',
        ])->assertOk();

        $created = Item::where('name', 'New Inventory Item')->sole();
        $this->assertSame(7, $created->qty);

        $this->postJson('/api/requests', [
            'type' => 'EDIT',
            'target_item_id' => $created->id,
            'proposed_data' => [
                'name' => 'Edited Inventory Item',
                'category' => 'Test',
                'qty' => 9,
                'price' => 14.50,
                'warehouse' => 'Warehouse B',
                'location' => 'Zone B',
                'status' => 'Active',
                'desc' => 'Edited through approval',
            ],
        ])->assertOk();

        $edit = InventoryRequest::where('outcome', 'PENDING')->sole();
        $this->postJson("/api/requests/{$edit->id}/resolve", [
            'decision' => 'approve',
        ])->assertOk();

        $this->assertSame('Edited Inventory Item', $created->fresh()->name);
        $this->assertSame(9, $created->fresh()->qty);

        $this->postJson('/api/requests', [
            'type' => 'DELETE',
            'target_item_id' => $created->id,
            'proposed_data' => [
                'name' => $created->name,
            ],
        ])->assertOk();

        $delete = InventoryRequest::where('outcome', 'PENDING')->sole();
        $this->postJson("/api/requests/{$delete->id}/resolve", [
            'decision' => 'void',
        ])->assertOk();

        $this->assertNotNull($created->fresh());
    }

    private function createItem(array $overrides = []): Item
    {
        return Item::create(array_merge([
            'id' => 'REG-ITEM-001',
            'name' => 'Regression Item',
            'category' => 'Test',
            'desc' => 'Regression fixture',
            'status' => 'Active',
            'qty' => 10,
            'price' => 10.00,
            'warehouse' => 'Warehouse A',
            'zone' => 'Zone A',
            'minLimit' => 0,
            'maxLimit' => 100,
            'auto_reorder' => false,
        ], $overrides));
    }

    private function createPurchaseOrder(Item $item, string $status, string $source): ApprovalRequest
    {
        $request = ApprovalRequest::create([
            'timestamp' => now()->format('Y-m-d H:i'),
            'requester' => 'Regression Operator',
            'details' => 'Regression purchase order',
            'supplier' => 'Regression Supplier',
            'warehouse' => 'Warehouse A',
            'status' => $status,
            'source' => $source,
        ]);

        $request->items()->create([
            'item_id' => $item->id,
            'qty' => 1,
        ]);

        return $request;
    }

    private function createMovementThroughEndpoint(Item $item, string $transactionId, int $quantity): void
    {
        $this->postJson('/api/stock-movements', [
            'tx_id' => $transactionId,
            'date' => '2026-07-25',
            'part_id' => $item->id,
            'type' => 'Stock-Out',
            'qty' => $quantity,
            'note' => 'Regression movement',
        ])->assertOk();
    }
}
