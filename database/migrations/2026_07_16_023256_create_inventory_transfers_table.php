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
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('equipment_id');
            $table->uuid('from_building_id');
            $table->uuid('from_room_id')->nullable();
            $table->uuid('to_building_id');
            $table->uuid('to_room_id')->nullable();
            $table->date('transfer_date');
            $table->text('reason');
            $table->uuid('performed_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->foreign('from_building_id')->references('id')->on('buildings')->onDelete('restrict');
            $table->foreign('from_room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('to_building_id')->references('id')->on('buildings')->onDelete('restrict');
            $table->foreign('to_room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transfers');
    }
};
