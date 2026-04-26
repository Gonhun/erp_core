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
        Schema::create('taxes_refund_definition', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tax_id')->constrained('taxes')->onDelete('cascade');
            $table->decimal('def_amount', 18, 2);
            $table->foreignUuid('account_id')->constrained('accounts');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes_refund_definition');
    }
};
