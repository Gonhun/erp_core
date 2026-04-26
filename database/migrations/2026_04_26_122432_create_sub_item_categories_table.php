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
        Schema::create('sub_item_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sub_category_name', 255)->unique()->index();
            $table->foreignUuid('item_category_id')->constrained('item_categories')->onDelete('cascade');
            $table->boolean('is_active')->default(true)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_item_categories');
    }
};
