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
        Schema::create('service_request_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_request_id');
            $table->uuid('assigned_to');
            $table->uuid('assigned_by');
            $table->timestamp('assigned_at')->useCurrent();
            $table->text('notes')->nullable();
            
            $table->foreign('service_request_id')->references('id')->on('service_requests')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_assignments');
    }
};
