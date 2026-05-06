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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('supplier_code', 255)->unique()->index();
            $table->string('supplier_name', 255)->index();
            
            // Contact & Address
            $table->text('supplier_address')->nullable();
            $table->string('supplier_region', 255)->index()->nullable();
            $table->string('supplier_state', 255)->index()->nullable();
            $table->string('supplier_postal_code', 12)->index()->nullable();
            $table->string('supplier_phone', 20)->nullable();
            $table->string('supplier_email', 255)->nullable();
            $table->string('supplier_website', 255)->nullable();
            
            // Tax Information
            $table->string('supplier_npwp', 255)->unique()->nullable();
            $table->string('supplier_nik', 16)->unique()->nullable();
            $table->string('tax_name', 255)->nullable(); // Legal tax name
            $table->text('tax_address')->nullable();
            $table->boolean('is_pkp')->default(false);
            $table->boolean('is_ppn')->default(false);
            
            // Flags
            $table->boolean('is_individual')->default(false);
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
        Schema::dropIfExists('suppliers');
    }
};
