<?php

// database/migrations/2026_07_13_000000_create_inventory_submodule_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Master Inventory Catalog
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->string('id')->primary(); // Using string IDs like 'PC-001'
            $table->string('name');
            $table->string('category');
            $table->integer('stock')->default(0);
            $table->decimal('price', 10, 2);
            $table->string('warehouse');
            $table->timestamps();
        });

        // 2. System Audit Logs
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->text('action');
            $table->timestamp('created_at')->useCurrent();
        });

        // 3. QC Inspections (Internal)
        Schema::create('qc_inspections', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g., REQ-I-101
            $table->string('op');
            $table->string('itemId');
            $table->string('product');
            $table->string('source');
            $table->string('action');
            $table->string('status')->default('Pending'); // Pending, Approved, Voided
            $table->timestamps();
        });

        // 4. RMA Requests (Manufacturer)
        Schema::create('rma_requests', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g., REQ-R-201
            $table->string('op');
            $table->string('itemId');
            $table->string('product');
            $table->string('vendor');
            $table->text('reasons');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        // 5. Finalized Returns Audit Log
        Schema::create('returns_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('op');
            $table->string('stream'); // Inspection, RMA
            $table->text('info');
            $table->string('outcome');
            $table->string('statusType'); // success, danger, neutral, void
            $table->timestamps();
        });

        // 6. Product Bundling Requests
        Schema::create('bundle_requests', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g., REQ-B-301
            $table->string('requester');
            $table->string('type'); // Pre-built, Custom Build
            $table->string('details');
            $table->json('recipe'); // Array of part IDs
            $table->string('status')->default('Pending'); // Pending, Approved, Voided
            $table->string('approver')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_requests');
        Schema::dropIfExists('returns_audit_logs');
        Schema::dropIfExists('rma_requests');
        Schema::dropIfExists('qc_inspections');
        Schema::dropIfExists('system_logs');
        Schema::dropIfExists('inventory_items');
    }
};
