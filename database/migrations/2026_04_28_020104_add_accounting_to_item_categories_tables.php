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
        Schema::table('item_categories', function (Blueprint $table) {
            $table->foreignUuid('income_account')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignUuid('expense_account')->nullable()->constrained('accounts')->onDelete('set null');
        });

        Schema::table('sub_item_categories', function (Blueprint $table) {
            $table->foreignUuid('income_account')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignUuid('expense_account')->nullable()->constrained('accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropForeign(['income_account']);
            $table->dropForeign(['expense_account']);
            $table->dropColumn(['income_account', 'expense_account']);
        });

        Schema::table('sub_item_categories', function (Blueprint $table) {
            $table->dropForeign(['income_account']);
            $table->dropForeign(['expense_account']);
            $table->dropColumn(['income_account', 'expense_account']);
        });
    }
};
