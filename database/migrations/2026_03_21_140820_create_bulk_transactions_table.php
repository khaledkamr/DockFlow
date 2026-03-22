<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_inventory_id')->constrained('bulk_inventory')->cascadeOnDelete();
            $table->enum('transaction_type', ['in', 'out']);
            $table->decimal('quantity', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_transaction');
    }
};
