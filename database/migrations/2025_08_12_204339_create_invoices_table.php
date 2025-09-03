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
            $table->string('code')->unique();
            $table->string('made_by');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['كاش', 'كريدت', 'تحويل بنكي']);
            $table->date('date');
            $table->enum('payment', ['تم الدفع', 'لم يتم الدفع']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
