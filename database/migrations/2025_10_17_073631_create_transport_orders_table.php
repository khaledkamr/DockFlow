<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('transaction_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('type');
            $table->date('date')->default(now());
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->integer('duration')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('diesel_cost', 10, 2)->default(0);
            $table->decimal('driver_wage', 10, 2)->default(0);
            $table->decimal('other_expenses', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->storedAs('diesel_cost + driver_wage + other_expenses');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_orders');
    }
};
