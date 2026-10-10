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
        Schema::create('resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('owner_type', ['user', 'school'])->default('school');
            $table->uuid('owner_id')->nullable()->comment('User ID for user-owned resources, null for school-owned');
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->default('good');
            $table->enum('status', ['available', 'borrowed', 'unavailable', 'maintenance'])->default('available');
            $table->json('availability_schedule')->nullable();
            $table->text('lending_conditions')->nullable();
            $table->json('photos')->nullable();
            $table->uuid('building_id')->nullable();
            $table->uuid('room_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');

            $table->index('owner_type');
            $table->index('owner_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
