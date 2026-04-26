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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('warehouse_code', 255)->unique()->index();
            $table->string('warehouse_name', 255)->index();
            $table->string('warehouse_short_name', 15)->nullable();
            $table->text('warehouse_address')->nullable();
            $table->string('shipment_type', 6)->index();
            $table->boolean('is_buy_resupply')->nullable();
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
        Schema::dropIfExists('warehouses');
    }
};
