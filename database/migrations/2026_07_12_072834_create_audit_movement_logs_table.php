<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_movement_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // APPROVED, VOIDED
            $table->string('name');
            $table->string('from_wh');
            $table->string('to_wh');
            $table->string('zone');
            $table->integer('qty');
            $table->date('raw_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_movement_logs');
    }
};