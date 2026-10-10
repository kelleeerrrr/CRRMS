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
        Schema::table('facilities', function (Blueprint $table) {
            // Make room_id nullable since we removed the location section
            $table->dropForeign(['room_id']);
            $table->uuid('room_id')->nullable()->change();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            
            // Rename description to location_address
            $table->renameColumn('description', 'location_address');
            
            // Add new fields
            $table->string('operating_days')->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->integer('max_reservation_duration')->nullable();
            $table->integer('advance_booking_requirement')->nullable();
            $table->string('person_in_charge')->nullable();
            $table->string('contact_information')->nullable();
            $table->text('special_instructions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->uuid('room_id')->nullable(false)->change();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            
            $table->renameColumn('location_address', 'description');
            
            $table->dropColumn([
                'operating_days',
                'opening_time',
                'closing_time',
                'max_reservation_duration',
                'advance_booking_requirement',
                'person_in_charge',
                'contact_information',
                'special_instructions'
            ]);
        });
    }
};
