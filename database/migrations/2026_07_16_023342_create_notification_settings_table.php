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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('reservation_approved')->default(true);
            $table->boolean('reservation_rejected')->default(true);
            $table->boolean('reservation_reminder')->default(true);
            $table->boolean('equipment_ready')->default(true);
            $table->boolean('return_reminder')->default(true);
            $table->boolean('overdue_reminder')->default(true);
            $table->boolean('maintenance_complete')->default(true);
            $table->boolean('new_message')->default(true);
            $table->boolean('announcement')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
