<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movement_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_inventory_item_id')->constrained('warehouse_inventory_items')->onDelete('cascade');
            $table->string('requester');
            $table->string('name');
            $table->string('from_wh');
            $table->string('from_zone');
            $table->string('to_wh');
            $table->string('to_zone');
            $table->integer('qty');
            $table->date('planned_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movement_requests');
    }
};