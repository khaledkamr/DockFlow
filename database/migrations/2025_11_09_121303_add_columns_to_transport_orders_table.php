<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_orders', function (Blueprint $table) {
            $table->string('driver_name')->nullable();
            $table->string('driver_contact')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->decimal('supplier_cost', 10, 2)->default(0);
            $table->decimal('client_cost', 10, 2)->default(0);

            $table->dropColumn('total_cost');
        });
    }

    public function down(): void
    {
        Schema::table('transport_orders', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'driver_contact', 'vehicle_plate', 'supplier_cost', 'client_cost']);
            $table->decimal('total_cost', 10, 2)->storedAs('diesel_cost + driver_wage + other_expenses');
        });
    }
};
