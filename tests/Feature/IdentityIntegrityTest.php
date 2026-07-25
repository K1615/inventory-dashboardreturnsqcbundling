<?php

namespace Tests\Feature;

use App\Models\ApprovalRequest;
use App\Models\BundleRequest;
use App\Models\InventoryRequest;
use App\Models\Item;
use App\Models\QcInspection;
use App\Models\ReturnsAuditLog;
use App\Models\RmaRequest;
use App\Models\ShipmentHandoff;
use App\Models\StockMovement;
use App\Models\StockMovementRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdentityIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Session Operator',
            'email' => 'session.operator@example.test',
        ]);
        $this->actingAs($this->user);
    }

    public function test_inventory_requestor_and_reviewer_cannot_be_spoofed(): void
    {
        $item = $this->createItem();

        $this->postJson('/api/requests', [
            'type' => 'EDIT',
            'requestor' => 'Malicious Requestor',
            'target_item_id' => $item->id,
            'proposed_data' => [
                'name' => $item->name,
                'category' => $item->category,
                'qty' => $item->qty,
                'price' => $item->price,
                'warehouse' => $item->warehouse,
                'location' => $item->zone,
                'status' => $item->status,
                'desc' => $item->desc,
            ],
        ])->assertOk();

        $inventoryRequest = InventoryRequest::sole();
        $this->assertSame($this->user->name, $inventoryRequest->requestor);

        $this->postJson("/api/requests/{$inventoryRequest->id}/resolve", [
            'decision' => 'void',
            'reviewer' => 'Malicious Reviewer',
        ])->assertOk();

        $this->assertSame($this->user->name, $inventoryRequest->fresh()->reviewer);
    }

    public function test_stock_movement_user_cannot_be_spoofed(): void
    {
        $item = $this->createItem();

        $this->postJson('/api/stock-movements', [
            'tx_id' => 'TX-AUTH-001',
            'date' => '2026-07-25',
            'part_id' => $item->id,
            'type' => 'Stock-In',
            'qty' => 2,
            'note' => 'Authenticated receipt',
            'user' => 'Malicious User',
        ])->assertOk();

        $this->assertSame($this->user->name, StockMovement::sole()->user);
    }

    public function test_warehouse_transfer_requester_cannot_be_spoofed(): void
    {
        $item = $this->createItem();

        $this->postJson('/warehouse-layout/request', [
            'itemId' => $item->id,
            'requester' => 'Malicious Requester',
            'toWh' => 'Warehouse B',
            'toZone' => 'Zone B',
            'qty' => 2,
            'date' => '2026-07-26',
        ])->assertOk();

        $this->assertSame($this->user->name, StockMovementRequest::sole()->requester);
    }

    public function test_returns_operators_and_resolvers_come_from_the_session(): void
    {
        $item = $this->createItem();

        $this->postJson('/inventory/api/inspection', [
            'itemId' => $item->id,
            'source' => 'Customer Aftersales',
            'outcome' => 'Good - Clear for Restock',
            'op' => 'Malicious Operator',
        ])->assertOk();

        $inspection = QcInspection::sole();
        $this->assertSame($this->user->name, $inspection->op);

        $this->postJson('/inventory/api/resolve-return', [
            'type' => 'Inspection',
            'id' => $inspection->id,
            'decision' => 'Voided',
            'operator' => 'Malicious Resolver',
        ])->assertOk();

        $this->assertSame($this->user->name, ReturnsAuditLog::sole()->op);

        $this->postJson('/inventory/api/rma', [
            'itemId' => $item->id,
            'vendor' => 'External Vendor',
            'reasons' => 'Physical Defect',
            'op' => 'Malicious RMA Operator',
        ])->assertOk();

        $this->assertSame($this->user->name, RmaRequest::sole()->op);
    }

    public function test_bundle_requester_and_approver_cannot_be_spoofed(): void
    {
        $item = $this->createItem();

        $this->postJson('/inventory/api/bundle', [
            'requester' => 'Malicious Requester',
            'type' => 'Custom Build',
            'details' => 'One-part test build',
            'recipe' => [$item->id],
        ])->assertOk();

        $bundle = BundleRequest::sole();
        $this->assertSame($this->user->name, $bundle->requester);

        $this->postJson('/inventory/api/resolve-bundle', [
            'id' => $bundle->id,
            'decision' => 'Voided',
            'approver' => 'Malicious Approver',
        ])->assertOk();

        $this->assertSame($this->user->name, $bundle->fresh()->approver);
    }

    public function test_purchase_order_requester_and_receiver_cannot_be_spoofed(): void
    {
        $item = $this->createItem();

        $this->postJson('/inventory/api/submit-po', [
            'details' => 'Authentication test order',
            'supplier' => 'External Supplier',
            'warehouse' => 'Warehouse A',
            'itemsArray' => [
                ['id' => $item->id, 'qty' => 3],
            ],
            'requester' => 'Malicious Requester',
        ])->assertOk();

        $purchaseOrder = ApprovalRequest::sole();
        $this->assertSame($this->user->name, $purchaseOrder->requester);

        $purchaseOrder->update(['status' => 'Ordered']);

        $this->postJson("/inventory/api/pipeline/{$purchaseOrder->reqId}/receive", [
            'created_by' => 'Malicious Receiver',
        ])->assertOk();

        $this->assertSame($this->user->name, ShipmentHandoff::sole()->created_by);
    }

    private function createItem(array $overrides = []): Item
    {
        return Item::create(array_merge([
            'id' => 'AUTH-ITEM-001',
            'name' => 'Authentication Test Item',
            'category' => 'Test',
            'desc' => 'Authentication regression fixture',
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
}
