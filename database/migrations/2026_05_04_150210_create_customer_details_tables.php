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
        // 1. Customer Accounting
        Schema::create('customer_accountings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignUuid('account_receivable')->constrained('accounts')->onDelete('cascade');
            $table->foreignUuid('account_payable')->constrained('accounts')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Customer Bank Accounts
        Schema::create('customer_bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('bank', 255);
            $table->string('bank_account_name', 255);
            $table->string('bank_account_number', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Customer Contacts
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('contact_type', 255)->index(); // e.g. Billing, Shipping, Other
            $table->string('customer_contact_address', 255)->nullable();
            $table->string('customer_contact_region', 255)->nullable();
            $table->string('customer_contact_state', 255)->nullable();
            $table->string('customer_contact_postal_code', 12)->nullable();
            $table->string('customer_contact_email', 255)->nullable();
            $table->string('customer_contact_phone', 16)->nullable();
            $table->string('customer_contact_mobile', 16)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_contacts');
        Schema::dropIfExists('customer_bank_accounts');
        Schema::dropIfExists('customer_accountings');
    }
};
