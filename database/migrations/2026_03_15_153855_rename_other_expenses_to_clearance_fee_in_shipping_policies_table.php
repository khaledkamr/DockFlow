<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_policies', function (Blueprint $table) {
            $table->dropColumn('total_cost');

            $table->renameColumn('other_expenses', 'clearance_fee');
            $table->decimal('late_fee', 10, 2)->nullable()->default(0)->after('clearance_fee');
            $table->decimal('commission', 10, 2)->nullable()->default(0)->after('late_fee');

            $table->decimal('total_cost', 10, 2)->storedAs('clearance_fee + late_fee + client_cost')->after('client_cost');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_policies', function (Blueprint $table) {
            $table->dropColumn('total_cost');

            $table->renameColumn('clearance_fee', 'other_expenses');
            $table->dropColumn('late_fee');
            $table->dropColumn('commission');

            $table->decimal('total_cost', 10, 2)->storedAs('other_expenses + client_cost')->after('client_cost');
        });
    }
};