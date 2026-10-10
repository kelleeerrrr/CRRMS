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
        Schema::table('resources', function (Blueprint $table) {
            $table->integer('total_quantity')->default(1)->after('description');
            $table->integer('available_quantity')->default(1)->after('total_quantity');
            $table->boolean('available_for_borrowing')->default(true)->after('status');
            $table->integer('max_borrowing_duration')->nullable()->after('available_for_borrowing');
            $table->string('pickup_location')->nullable()->after('max_borrowing_duration');
            $table->text('borrowing_restrictions')->nullable()->after('pickup_location');
            $table->string('contact_number')->nullable()->after('borrowing_restrictions');
            $table->text('additional_notes')->nullable()->after('contact_number');
            $table->string('photo')->nullable()->after('additional_notes');
        });

        // Update condition enum values - drop existing constraint first
        DB::statement("ALTER TABLE resources DROP CONSTRAINT IF EXISTS resources_condition_check");
        DB::statement("ALTER TABLE resources ALTER COLUMN condition TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE resources ADD CONSTRAINT resources_condition_check CHECK (condition IN ('New', 'Good', 'Fair', 'Poor', 'Damaged'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn([
                'total_quantity',
                'available_quantity',
                'available_for_borrowing',
                'max_borrowing_duration',
                'pickup_location',
                'borrowing_restrictions',
                'contact_number',
                'additional_notes',
                'photo'
            ]);
        });

        // Revert condition enum
        DB::statement("ALTER TABLE resources DROP CONSTRAINT resources_condition_check");
        DB::statement("ALTER TABLE resources ALTER COLUMN condition TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE resources ADD CONSTRAINT resources_condition_check CHECK (condition IN ('excellent', 'good', 'fair', 'poor', 'damaged'))");
    }
};
