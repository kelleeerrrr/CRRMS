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
        Schema::create('equipment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id');
            $table->string('asset_number')->unique();
            $table->string('barcode')->unique();
            $table->string('qr_code')->unique();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique()->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('supplier')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->uuid('building_id');
            $table->uuid('room_id')->nullable();
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->default('good');
            $table->enum('status', ['available', 'borrowed', 'reserved', 'maintenance', 'lost', 'retired'])->default('available');
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('category_id')->references('id')->on('equipment_categories')->onDelete('restrict');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            
            $table->index('asset_number');
            $table->index('barcode');
            $table->index('qr_code');
            $table->index('serial_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
