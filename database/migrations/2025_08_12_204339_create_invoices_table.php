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
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->enum('type', ['خدمات', 'تخزين'])->default('تخزين');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->enum('payment_method', ['كاش', 'آجل', 'تحويل بنكي'])->default('آجل');
            $table->date('date');
            $table->enum('payment', ['تم الدفع', 'لم يتم الدفع']);
            $table->string('notes')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
