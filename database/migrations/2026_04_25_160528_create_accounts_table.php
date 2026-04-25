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
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('account_code', 255)->index();
            $table->string('account_name', 255)->index();
            $table->foreignUuid('group_account_id')->index()->constrained('group_accounts');
            $table->string('balance_type', 6)->index();
            $table->integer('account_level');
            $table->boolean('is_reconciliation')->default(false)->index();
            $table->uuid('reconciliation_type')->nullable();
            $table->string('account_currency', 5)->nullable();
            $table->boolean('is_parent')->default(false);
            $table->uuid('parent_id')->nullable();
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
        Schema::dropIfExists('accounts');
    }
};
