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
        Schema::create('uom_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category_name', 255)->unique()->index();
            $table->string('base_uom_name', 255);
            $table->string('base_uom', 50);
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
        Schema::dropIfExists('uom_categories');
    }
};
