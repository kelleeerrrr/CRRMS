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
            $table->string('borrower_name')->after('user_id');
            $table->string('contact_number')->after('borrower_name');
            $table->string('email')->after('contact_number');
            $table->integer('quantity')->default(1)->after('email');
            $table->string('intended_use')->nullable()->after('purpose');
            
            // Rename columns
            $table->renameColumn('borrow_date', 'start_date');
            $table->renameColumn('return_date', 'end_date');
            
            // Change date columns to datetime
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->change();
            $table->dateTime('actual_return_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_borrow_requests', function (Blueprint $table) {
            $table->dropColumn([
                'borrower_name',
                'contact_number',
                'email',
                'quantity',
                'intended_use'
            ]);
            
            // Revert column names
            $table->renameColumn('start_date', 'borrow_date');
            $table->renameColumn('end_date', 'return_date');
            
            // Revert to date
            $table->date('borrow_date')->change();
            $table->date('return_date')->change();
            $table->date('actual_return_date')->nullable()->change();
        });
    }
};
