<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->foreignId('credit_account_id')->after('transaction_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('debit_account_id')->after('credit_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->boolean('is_posted')->default(false)->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropForeign(['credit_account_id']);
            $table->dropForeign(['debit_account_id']);
            $table->dropColumn(['credit_account_id', 'debit_account_id', 'is_posted']);
        });
    }
};
