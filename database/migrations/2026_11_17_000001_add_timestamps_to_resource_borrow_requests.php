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
        Schema::table('resource_borrow_requests', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('notes');
            $table->uuid('cancelled_by')->nullable()->after('rejection_reason');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            $table->timestamp('checked_out_at')->nullable()->after('cancelled_at');
            $table->uuid('checked_out_by')->nullable()->after('checked_out_at');
            $table->timestamp('returned_at')->nullable()->after('checked_out_by');
            $table->uuid('returned_by')->nullable()->after('returned_at');

            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checked_out_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_borrow_requests', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropForeign(['checked_out_by']);
            $table->dropForeign(['returned_by']);
            $table->dropColumn([
                'approved_at',
                'rejection_reason',
                'cancelled_by',
                'cancelled_at',
                'checked_out_at',
                'checked_out_by',
                'returned_at',
                'returned_by',
            ]);
        });
    }
};
