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
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['reservation', 'inventory', 'borrow', 'building_utilization', 'damage', 'maintenance']);
            $table->enum('format', ['pdf', 'excel', 'csv']);
            $table->json('parameters');
            $table->string('file_path');
            $table->integer('file_size');
            $table->uuid('generated_by');
            $table->timestamp('generated_at')->useCurrent();
            
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
