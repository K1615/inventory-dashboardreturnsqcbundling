<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Persisted stock alerts
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('item_id'); // Renamed from inventory_item_id
            $table->string('type'); 
            $table->string('severity'); 
            $table->integer('current_qty');
            $table->integer('threshold_qty');
            $table->string('status')->default('active'); 
            $table->string('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Updated foreign key to point to `items`
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });

        // Purchase order approval pipeline
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id('reqId');
            $table->string('timestamp');
            $table->string('requester');
            $table->string('details');
            $table->string('supplier');
            $table->string('warehouse');
            $table->string('status')->default('Pending'); 
            $table->string('source')->default('manual'); 
            $table->unsignedBigInteger('triggered_by_alert_id')->nullable();
            $table->timestamps();

            $table->foreign('triggered_by_alert_id')->references('id')->on('stock_alerts')->nullOnDelete();
        });

        // Relational PO line items
        Schema::create('approval_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_request_id');
            $table->string('item_id'); // Renamed from inventory_item_id
            $table->integer('qty');
            $table->timestamps();

            $table->foreign('approval_request_id')->references('reqId')->on('approval_requests')->cascadeOnDelete();
            // Updated foreign key to point to `items`
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });

        // Handoff record for received shipments
        Schema::create('shipment_handoffs', function (Blueprint $table) {
            $table->id();
            $table->string('item_id'); // Renamed from inventory_item_id
            $table->string('type'); 
            $table->integer('qty');
            $table->string('source_type'); 
            $table->unsignedBigInteger('source_id')->nullable(); 
            $table->string('created_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Updated foreign key to point to `items`
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_handoffs');
        Schema::dropIfExists('approval_request_items');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('stock_alerts');
    }
};