<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_borrow_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('borrow_request_id');
            $table->uuid('equipment_id');
            $table->integer('quantity');
            $table->string('condition_at_borrow')->default('good');
            $table->string('condition_at_return')->nullable();
            $table->text('damage_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('borrow_request_id')->references('id')->on('equipment_borrow_requests')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_borrow_items');
    }
};
