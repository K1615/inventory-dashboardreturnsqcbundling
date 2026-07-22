<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // ADD, EDIT, DELETE
            $table->string('requestor');
            $table->string('reviewer')->nullable();
            $table->string('outcome')->default('PENDING'); // PENDING, APPROVED, VOIDED
            $table->string('target_item_id')->nullable(); // For EDIT/DELETE operations
            
            // Structured data payload of the proposed change details
            $table->json('proposed_data'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};