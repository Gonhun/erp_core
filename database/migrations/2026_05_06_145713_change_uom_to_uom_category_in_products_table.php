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
        Schema::table('products', function (Blueprint $table) {
            // Drop old foreign keys
            $table->dropForeign(['uom_id']);
            $table->dropForeign(['purchase_uom_id']);
            
            // Rename columns
            $table->renameColumn('uom_id', 'uom_category_id');
            $table->renameColumn('purchase_uom_id', 'purchase_uom_category_id');
        });

        Schema::table('products', function (Blueprint $table) {
            // Add new foreign keys pointing to uom_categories
            $table->foreign('uom_category_id')->references('id')->on('uom_categories')->onDelete('cascade');
            $table->foreign('purchase_uom_category_id')->references('id')->on('uom_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['uom_category_id']);
            $table->dropForeign(['purchase_uom_category_id']);
            
            $table->renameColumn('uom_category_id', 'uom_id');
            $table->renameColumn('purchase_uom_category_id', 'purchase_uom_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('uom_id')->references('id')->on('uoms')->onDelete('cascade');
            $table->foreign('purchase_uom_id')->references('id')->on('uoms')->onDelete('cascade');
        });
    }
};
