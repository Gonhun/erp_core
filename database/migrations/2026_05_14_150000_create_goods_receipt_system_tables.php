<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('gr_number')->unique()->index();
            $table->foreignUuid('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->string('currency', 10);
            $table->string('pib_no', 255)->nullable();
            $table->string('invoice_control', 20)->default('no'); // no, to_invoice, invoiced
            $table->date('received_date')->nullable();
            $table->string('status', 20)->default('draft'); // draft, waiting, ready, done, cancel
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('goods_receipt_id')->constrained('goods_receipts')->onDelete('cascade');
            $table->foreignUuid('purchase_order_item_id')->constrained('purchase_order_items')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('qty_ordered', 15, 2);
            $table->decimal('qty_received', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_moves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->index(); // e.g. GR Number or PO Number
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->decimal('qty', 15, 2); // Positive for in, negative for out
            $table->string('type', 20); // receipt, delivery, internal, adjustment
            $table->timestamps();
        });

        Schema::create('product_stocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->decimal('qty_on_hand', 15, 2)->default(0);
            $table->unique(['product_id', 'warehouse_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
        Schema::dropIfExists('stock_moves');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
    }
};
