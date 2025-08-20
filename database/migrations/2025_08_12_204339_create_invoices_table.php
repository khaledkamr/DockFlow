<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('base_price', 10, 2);
            $table->decimal('late_fee_total', 10, 2)->default(0);
            $table->decimal('tax_total', 10, 2);
            $table->decimal('grand_total', 10, 2);
            $table->enum('payment_method', ['كاش', 'كريدت', 'تحويل بنكي']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
