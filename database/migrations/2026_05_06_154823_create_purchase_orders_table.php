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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('po_number', 50)->unique()->index();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->date('order_date')->index();
            $table->date('expected_arrival')->nullable();
            
            // Flow: Quotation (draft) -> RFQ (sent) -> PO (purchase)
            $table->string('status', 20)->default('draft')->index(); // draft, sent, purchase, cancel, done
            
            // Financials
            $table->decimal('untaxed_amount', 20, 4)->default(0);
            $table->decimal('tax_amount', 20, 4)->default(0);
            $table->decimal('total_amount', 20, 4)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
