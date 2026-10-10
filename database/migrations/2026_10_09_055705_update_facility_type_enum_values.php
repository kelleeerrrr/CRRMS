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
        DB::statement("ALTER TABLE facilities DROP CONSTRAINT IF EXISTS facilities_type_check");
        DB::statement("ALTER TABLE facilities ALTER COLUMN type TYPE VARCHAR(100)");
        DB::statement("ALTER TABLE facilities ADD CONSTRAINT facilities_type_check CHECK (type IN ('Classroom', 'Laboratory', 'Auditorium', 'Conference Room', 'Function Room', 'Sports Facility', 'Other'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE facilities DROP CONSTRAINT facilities_type_check");
        DB::statement("ALTER TABLE facilities ALTER COLUMN type TYPE VARCHAR(255) CHECK (type IN ('classroom', 'computer_lab', 'science_lab', 'engineering_lab', 'avr', 'library_discussion_room', 'auditorium', 'gymnasium', 'function_hall', 'conference_room', 'multi_purpose_hall', 'quadrangle', 'covered_court', 'open_grounds'))");
    }
};
