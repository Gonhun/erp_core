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
        Schema::create('uoms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('uom_name', 255)->unique()->index();
            $table->foreignUuid('uom_category_id')->constrained('uom_categories')->onDelete('cascade');
            $table->string('uom_type', 50)->index(); // Reference, Bigger, Smaller
            $table->decimal('ratio', 16, 4);
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
        Schema::dropIfExists('uoms');
    }
};
