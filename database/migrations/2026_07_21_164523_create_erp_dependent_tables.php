<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // System Audit Logs
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->text('action');
            $table->timestamp('created_at')->useCurrent();
        });

        // QC Inspections (Internal)
        Schema::create('qc_inspections', function (Blueprint $table) {
            $table->string('id')->primary(); 
            $table->string('op');
            $table->string('itemId'); // Will map to `items` string ID
            $table->string('product');
            $table->string('source');
            $table->string('action');
            $table->string('status')->default('Pending'); 
            $table->timestamps();
        });

        // RMA Requests (Manufacturer)
        Schema::create('rma_requests', function (Blueprint $table) {
            $table->string('id')->primary(); 
            $table->string('op');
            $table->string('itemId'); // Will map to `items` string ID
            $table->string('product');
            $table->string('vendor');
            $table->text('reasons');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        // Finalized Returns Audit Log
        Schema::create('returns_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('op');
            $table->string('stream'); 
            $table->text('info');
            $table->string('outcome');
            $table->string('statusType'); 
            $table->timestamps();
        });

        // Product Bundling Requests
        Schema::create('bundle_requests', function (Blueprint $table) {
            $table->string('id')->primary(); 
            $table->string('requester');
            $table->string('type'); 
            $table->string('details');
            $table->json('recipe'); 
            $table->string('status')->default('Pending'); 
            $table->string('approver')->nullable();
            $table->timestamps();
        });

        // Stock Movements
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('tx_id');
            $table->string('item_id'); 
            $table->string('type');
            $table->integer('qty');
            $table->text('note')->nullable();
            $table->string('status');
            $table->string('user');
            $table->timestamps();

            // Unified item_id foreign key constraint
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('bundle_requests');
        Schema::dropIfExists('returns_audit_logs');
        Schema::dropIfExists('rma_requests');
        Schema::dropIfExists('qc_inspections');
        Schema::dropIfExists('system_logs');
    }
};