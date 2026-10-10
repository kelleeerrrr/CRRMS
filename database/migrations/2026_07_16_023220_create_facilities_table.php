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
        Schema::create('facilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->string('name');
            $table->enum('type', ['classroom', 'computer_lab', 'science_lab', 'engineering_lab', 'avr', 'library_discussion_room', 'auditorium', 'gymnasium', 'function_hall', 'conference_room', 'multi_purpose_hall', 'quadrangle', 'covered_court', 'open_grounds']);
            $table->enum('category', ['academic', 'events', 'outdoor']);
            $table->integer('capacity')->nullable();
            $table->text('description')->nullable();
            $table->json('amenities')->nullable();
            $table->json('photos')->nullable();
            $table->text('rules')->nullable();
            $table->json('availability_schedule')->nullable();
            $table->enum('status', ['available', 'unavailable', 'maintenance'])->default('available');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
