<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->string('tx_id')->primary(); // Matching 'TX-10401'
            $table->date('date');
            $table->string('part_id');
            $table->string('type'); // Stock-In, Stock-Out, etc.
            $table->integer('qty');
            $table->text('note');
            $table->string('status')->default('Pending'); // Pending, Approved, Voided
            $table->string('user');
            $table->timestamps();

            $table->foreign('part_id')->references('id')->on('parts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};