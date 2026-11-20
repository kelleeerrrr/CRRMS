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
        Schema::table('conversations', function (Blueprint $table) {
            $table->enum('resource_type', ['facility', 'equipment', 'resource'])->nullable()->after('is_pinned');
            $table->uuid('resource_id')->nullable()->after('resource_type');
            $table->uuid('transaction_id')->nullable()->after('resource_id');
            $table->enum('transaction_type', ['facility_reservation', 'equipment_borrow_request', 'resource_borrow_request'])->nullable()->after('transaction_id');
            
            $table->index(['resource_type', 'resource_id']);
            $table->index(['transaction_type', 'transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['resource_type', 'resource_id']);
            $table->dropIndex(['transaction_type', 'transaction_id']);
            $table->dropColumn(['resource_type', 'resource_id', 'transaction_id', 'transaction_type']);
        });
    }
};
