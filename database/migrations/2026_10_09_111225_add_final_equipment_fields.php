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
        Schema::table('equipment', function (Blueprint $table) {
            // Add new fields
            $table->string('name')->nullable()->after('id');
            $table->string('equipment_code')->nullable()->after('asset_number');
            $table->enum('equipment_category', ['Computer', 'Projector', 'Audio Equipment', 'Laboratory Equipment', 'Sports Equipment', 'Office Equipment', 'Other'])->nullable()->after('category_id');
            $table->string('location_building')->nullable()->after('building_id');
            $table->string('room_storage')->nullable()->after('room_id');
            $table->integer('total_quantity')->default(1)->after('condition');
            $table->enum('availability_status', ['Available', 'Unavailable', 'Under Maintenance', 'Retired'])->default('Available')->after('total_quantity');
            $table->boolean('available_for_borrowing')->default(true)->after('availability_status');
            $table->integer('max_borrowing_duration')->nullable()->after('available_for_borrowing');
            $table->integer('advance_booking_requirement')->nullable()->after('max_borrowing_duration');
            $table->text('borrowing_restrictions')->nullable()->after('advance_booking_requirement');
            $table->string('responsible_office')->nullable()->after('borrowing_restrictions');
            $table->text('additional_notes')->nullable()->after('responsible_office');
            $table->string('equipment_photo')->nullable()->after('additional_notes');
            
            // Make some existing fields nullable
            $table->string('brand')->nullable()->change();
            $table->string('model')->nullable()->change();
            $table->string('serial_number')->nullable()->change();
            $table->date('purchase_date')->nullable()->change();
            $table->date('warranty_expiry')->nullable()->change();
            $table->string('supplier')->nullable()->change();
            $table->decimal('cost', 10, 2)->nullable()->change();
            $table->uuid('building_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'name',
                'equipment_code',
                'equipment_category',
                'location_building',
                'room_storage',
                'total_quantity',
                'availability_status',
                'available_for_borrowing',
                'max_borrowing_duration',
                'advance_booking_requirement',
                'borrowing_restrictions',
                'responsible_office',
                'additional_notes',
                'equipment_photo',
            ]);
        });
    }
};
