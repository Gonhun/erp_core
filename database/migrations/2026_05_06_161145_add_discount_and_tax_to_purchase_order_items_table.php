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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('discount', 5, 2)->default(0)->after('unit_price'); // Percentage discount
            $table->foreignUuid('tax_id')->nullable()->after('discount')->constrained('taxes')->onDelete('set null');
            $table->decimal('tax_amount', 20, 4)->default(0)->after('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['discount', 'tax_id', 'tax_amount']);
        });
    }
};
