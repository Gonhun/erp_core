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
        // 1. Supplier Accounting
        Schema::create('supplier_accountings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignUuid('account_receivable')->constrained('accounts')->onDelete('cascade');
            $table->foreignUuid('account_payable')->constrained('accounts')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Supplier Bank Accounts
        Schema::create('supplier_bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('bank', 255);
            $table->string('bank_account_name', 255);
            $table->string('bank_account_number', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Supplier Contacts
        Schema::create('supplier_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('contact_type', 255)->index(); // Billing, Shipping, Contact Person
            $table->string('contact_name', 255);
            $table->string('supplier_contact_address', 255)->nullable();
            $table->string('supplier_contact_region', 255)->nullable();
            $table->string('supplier_contact_state', 255)->nullable();
            $table->string('supplier_contact_postal_code', 12)->nullable();
            $table->string('supplier_contact_email', 255)->nullable();
            $table->string('supplier_contact_phone', 20)->nullable();
            $table->string('supplier_contact_mobile', 20)->nullable();
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
        Schema::dropIfExists('supplier_contacts');
        Schema::dropIfExists('supplier_bank_accounts');
        Schema::dropIfExists('supplier_accountings');
    }
};
