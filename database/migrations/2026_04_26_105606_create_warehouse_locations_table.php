<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('location_code', 255)->unique()->index();
            $table->string('location_name', 255)->index();
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignUuid('location_type')->constrained('location_categories')->onDelete('cascade');
            $table->boolean('is_parent')->nullable();
            $table->uuid('parent_location')->nullable();
            $table->boolean('is_negative_stock')->nullable();
            $table->boolean('is_scrap_location')->nullable();
            $table->boolean('is_return_location')->nullable();
            $table->boolean('is_replenish')->nullable();
            $table->integer('inventory_frequency')->nullable();
            $table->text('location_note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};
