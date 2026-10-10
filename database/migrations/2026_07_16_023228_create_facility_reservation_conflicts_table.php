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
        Schema::create('facility_reservation_conflicts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reservation_id');
            $table->uuid('conflicting_reservation_id');
            $table->enum('conflict_type', ['time_overlap', 'facility_unavailable', 'capacity_exceeded']);
            $table->boolean('resolved')->default(false);
            $table->uuid('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('reservation_id')->references('id')->on('facility_reservations')->onDelete('cascade');
            $table->foreign('conflicting_reservation_id')->references('id')->on('facility_reservations')->onDelete('cascade');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_reservation_conflicts');
    }
};
