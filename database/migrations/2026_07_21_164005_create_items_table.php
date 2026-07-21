<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            // Primary Identifier
            $table->string('id')->primary();

            // Core Details
            $table->string('name');
            $table->string('category'); 
            $table->text('desc')->nullable();
            $table->string('status')->default('Active');
            
            // Quantities and Pricing
            $table->integer('qty')->default(0); 
            $table->decimal('price', 10, 2)->default(0.00);

            // Warehouse & Location Mapping
            $table->string('warehouse');
            $table->string('zone')->nullable(); 
            $table->timestamp('last_moved')->nullable();

            // Alerts & Reorder Limits 
            $table->integer('minLimit')->default(0);
            $table->integer('maxLimit')->default(0);
            $table->boolean('auto_reorder')->default(false);
            $table->integer('reorder_qty')->nullable();

            // Specific Overrides 
            $table->date('exp_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};