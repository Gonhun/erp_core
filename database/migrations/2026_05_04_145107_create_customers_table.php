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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('customer_code', 255)->unique()->index();
            $table->string('customer_name', 255)->index();
            $table->text('customer_address')->nullable();
            $table->string('customer_region', 255)->index()->nullable();
            $table->string('customer_state', 255)->index()->nullable();
            $table->string('customer_postal_code', 12)->index()->nullable();
            
            // Tax Information (Indonesian Context)
            $table->string('customer_npwp', 255)->unique()->nullable();
            $table->string('customer_nik', 16)->unique()->nullable()->comment('Indonesian ID card for individuals');
            $table->string('tax_name', 255)->nullable()->comment('Name registered in NPWP');
            $table->text('tax_address')->nullable()->comment('Address registered in NPWP');
            
            // Contact Information
            $table->string('customer_phone', 16)->nullable();
            $table->string('customer_mobile', 16)->nullable();
            $table->string('customer_email', 255)->nullable();
            $table->string('customer_website', 255)->nullable();
            
            // Flags
            $table->boolean('is_individual')->default(false);
            $table->boolean('is_company')->default(false);
            $table->boolean('is_ppn')->default(false);
            $table->boolean('is_pkp')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Financial Suggestion
            $table->decimal('credit_limit', 16, 2)->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
