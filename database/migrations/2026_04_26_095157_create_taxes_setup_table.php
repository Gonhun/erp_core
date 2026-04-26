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
        Schema::create('taxes_setup', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tax_id')->constrained('taxes')->onDelete('cascade');
            $table->string('tax_label', 255)->index();
            $table->string('tax_country', 255)->index()->nullable();
            $table->boolean('is_included_price')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes_setup');
    }
};
