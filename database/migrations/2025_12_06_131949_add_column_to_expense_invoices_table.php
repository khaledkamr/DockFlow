<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_invoices', function (Blueprint $table) {
            $table->foreignId('expense_account_id')->nullable()->constrained('accounts')->nullOnDelete()->after('supplier_invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('expense_invoices', function (Blueprint $table) {
            $table->dropForeign(['expense_account_id']);
            $table->dropColumn('expense_account_id');
        });
    }
};
