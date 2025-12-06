<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_orders', function (Blueprint $table) {
            $table->string('distance')->nullable()->after('duration');
            $table->decimal('total_cost', 10, 2)->storedAs('other_expenses + client_cost')->after('client_cost');
        });
    }

    public function down(): void
    {
        Schema::table('transport_orders', function (Blueprint $table) {
            $table->dropColumn(['distance', 'total_cost']);
        });
    }
};
