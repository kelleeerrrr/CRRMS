<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE equipment DROP CONSTRAINT IF EXISTS equipment_condition_check");
        DB::statement("ALTER TABLE equipment ADD CONSTRAINT equipment_condition_check CHECK (condition IN ('New', 'Good', 'Fair', 'Poor', 'Damaged'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE equipment DROP CONSTRAINT IF EXISTS equipment_condition_check");
        DB::statement("ALTER TABLE equipment ADD CONSTRAINT equipment_condition_check CHECK (condition IN ('excellent', 'good', 'fair', 'poor', 'damaged'))");
    }
};
