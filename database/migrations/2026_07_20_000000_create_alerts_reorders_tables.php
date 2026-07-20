<?php

// database/migrations/2026_07_20_000000_create_alerts_reorders_tables.php
//
// Adds the Alerts & Reorders submodule on top of the existing inventory
// tables. Reuses the existing `inventory_items` table (and its `stock`
// column) and the existing `system_logs` table for the activity trail,
// instead of introducing parallel tables — this submodule is one part of
// the same app, not a standalone system.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Extend the master catalog with reorder thresholds.
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->integer('minLimit')->default(0)->after('stock');
            $table->integer('maxLimit')->default(0)->after('minLimit');
            $table->boolean('auto_reorder')->default(false)->after('maxLimit');
            $table->integer('reorder_qty')->nullable()->after('auto_reorder');
        });

        // Persisted stock alerts (out_of_stock / low_stock / overstock).
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_item_id');
            $table->string('type'); // out_of_stock, low_stock, overstock
            $table->string('severity'); // critical, high, medium
            $table->integer('current_qty');
            $table->integer('threshold_qty');
            $table->string('status')->default('active'); // active, acknowledged, resolved
            $table->string('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->cascadeOnDelete();
        });

        // Purchase order approval pipeline: Draft -> Pending -> Ordered -> Received (or Voided).
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id('reqId');
            $table->string('timestamp');
            $table->string('requester');
            $table->string('details');
            $table->string('supplier');
            $table->string('warehouse');
            $table->string('status')->default('Pending'); // Draft, Pending, Ordered, Received, Voided
            $table->string('source')->default('manual'); // manual, auto
            $table->unsignedBigInteger('triggered_by_alert_id')->nullable();
            $table->timestamps();

            $table->foreign('triggered_by_alert_id')->references('id')->on('stock_alerts')->nullOnDelete();
        });

        // Relational PO line items (replaces a raw JSON blob approach).
        Schema::create('approval_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_request_id');
            $table->string('inventory_item_id');
            $table->integer('qty');
            $table->timestamps();

            $table->foreign('approval_request_id')->references('reqId')->on('approval_requests')->cascadeOnDelete();
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->cascadeOnDelete();
        });

        // Handoff record for received shipments. Applying this to real
        // stock counts is intentionally left to a separate Stock Movements
        // submodule — this table just records that a shipment happened.
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_item_id');
            $table->string('type'); // e.g. "receipt"
            $table->integer('qty');
            $table->string('source_type'); // e.g. "purchase_order"
            $table->unsignedBigInteger('source_id')->nullable(); // e.g. approval_requests.reqId
            $table->string('created_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('approval_request_items');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('stock_alerts');

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn(['minLimit', 'maxLimit', 'auto_reorder', 'reorder_qty']);
        });
    }
};
