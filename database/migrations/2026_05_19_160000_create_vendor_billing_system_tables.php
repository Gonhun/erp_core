<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Vendor Bills (Header)
        Schema::create('vendor_bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('bill_number')->unique()->index();
            $table->string('vendor_bill_number')->nullable()->index(); // Supplier's original invoice number
            $table->foreignUuid('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->date('bill_date')->index();
            $table->date('due_date')->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->decimal('untaxed_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('draft'); // draft, posted, paid, cancel
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Vendor Bill Items (Lines)
        Schema::create('vendor_bill_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vendor_bill_id')->constrained('vendor_bills')->onDelete('cascade');
            $table->foreignUuid('purchase_order_item_id')->nullable()->constrained('purchase_order_items')->onDelete('set null');
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->string('description', 255)->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->foreignUuid('tax_id')->nullable()->constrained('taxes')->onDelete('set null');
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->foreignUuid('account_id')->nullable()->constrained('accounts')->onDelete('set null'); // Expense or Asset Account
            $table->timestamps();
        });

        // 3. Journal Entries (General Ledger Header)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entry_number')->unique()->index();
            $table->date('entry_date')->index();
            $table->string('reference')->nullable()->index(); // e.g. Bill No or Payment No
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('draft'); // draft, posted, cancel
            $table->timestamps();
        });

        // 4. Journal Items (General Ledger Lines)
        Schema::create('journal_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignUuid('account_id')->constrained('accounts')->onDelete('cascade');
            $table->uuid('partner_id')->nullable()->index(); // Can reference Supplier or Customer ID
            $table->string('partner_type', 50)->nullable();  // 'supplier' or 'customer'
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('name', 255)->nullable(); // Line description
            $table->timestamps();
        });

        // 5. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number')->unique()->index();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignUuid('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->date('payment_date')->index();
            $table->string('currency', 10)->default('IDR');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 50); // Cash, Bank, Check, Transfer
            $table->foreignUuid('journal_account_id')->constrained('accounts')->onDelete('restrict'); // Cash or Bank Account
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('draft'); // draft, posted, cancel
            $table->timestamps();
        });

        // 6. Payment Allocations (Matching Payments to Bills)
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignUuid('vendor_bill_id')->constrained('vendor_bills')->onDelete('cascade');
            $table->decimal('amount_allocated', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('journal_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('vendor_bill_items');
        Schema::dropIfExists('vendor_bills');
    }
};
