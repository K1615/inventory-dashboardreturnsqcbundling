<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movement_requests', function (Blueprint $table) {
            $table->id();
            $table->string('item_id'); // String type to match your ITEM-001 format
            $table->string('requester');
            $table->string('from_wh');
            $table->string('from_zone');
            $table->string('to_wh');
            $table->string('to_zone');
            $table->integer('qty');
            $table->date('planned_date');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movement_requests');
    }
};
