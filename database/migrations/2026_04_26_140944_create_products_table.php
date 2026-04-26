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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_code', 255)->unique()->index();
            $table->string('product_name', 255)->index();
            $table->foreignUuid('product_type_id')->constrained('product_types')->onDelete('cascade');
            $table->string('invoicing_policy', 15)->index()->nullable();
            $table->foreignUuid('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignUuid('uom_id')->constrained('uoms')->onDelete('cascade');
            $table->foreignUuid('purchase_uom_id')->constrained('uoms')->onDelete('cascade');
            $table->foreignUuid('item_category_id')->constrained('item_categories')->onDelete('cascade');
            $table->foreignUuid('sub_item_id')->constrained('sub_item_categories')->onDelete('cascade');
            $table->string('part_number', 255)->index();
            $table->text('product_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
