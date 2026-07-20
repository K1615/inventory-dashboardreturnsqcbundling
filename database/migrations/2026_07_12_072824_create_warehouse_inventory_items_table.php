<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('warehouse_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // CPU, GPU, Storage, RAM
            $table->string('name');
            $table->string('warehouse'); // Main Warehouse, North Branch, East Hub
            $table->string('zone'); // Zone A, B, C, D, E
            $table->integer('qty')->default(0);
            $table->timestamp('last_moved')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_inventory_items');
    }
};