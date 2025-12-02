<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->string('type')->after('code');

            $table->string('description')->nullable()->change();

            // $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');

            $table->foreignId('credit_account_id')->nullable()->after('description')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('debit_account_id')->nullable()->after('credit_account_id')->constrained('accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('type', ['سند قبض نقدي', 'سند قبض بشيك', 'سند صرف نقدي', 'سند صرف بشيك']);
            $table->dropColumn('type');

            $table->string('description')->change();

            $table->foreignId('account_id')->constrained()->cascadeOnDelete();

            $table->dropForeign(['credit_account_id']);
            $table->dropForeign(['debit_account_id']);
            $table->dropColumn(['credit_account_id', 'debit_account_id']);
        });
    }
};
