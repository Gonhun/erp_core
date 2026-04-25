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
        Schema::create('taxes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tax_name', 255)->index();
            $table->integer('tax_computation')->index()->nullable();
            $table->string('tax_type', 255)->index()->nullable();
            $table->string('tax_scope', 12)->index()->nullable();
            $table->double('tax_amount', 18, 2);
            $table->boolean('is_ppn')->default(false);
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
        Schema::dropIfExists('taxes');
    }
};
