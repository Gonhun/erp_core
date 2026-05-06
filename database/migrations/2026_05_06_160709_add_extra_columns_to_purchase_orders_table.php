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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->date('order_deadline')->nullable()->after('order_date');
            $table->string('currency', 10)->default('IDR')->after('expected_arrival');
            $table->foreignUuid('warehouse_id')->nullable()->after('currency')->constrained('warehouses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['order_deadline', 'currency', 'warehouse_id']);
        });
    }
};
