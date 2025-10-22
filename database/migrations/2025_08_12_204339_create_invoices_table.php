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
            $table->enum('type', ['خدمات', 'تخزين', 'تخليص'])->default('تخزين');
            $table->decimal('amount_before_tax', 10, 2)->default(0);
            $table->decimal('tax', 5, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('amount_after_discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_method', ['كاش', 'آجل', 'تحويل بنكي'])->default('آجل');
            $table->timestamp('date')->default(now());
            $table->enum('payment', ['تم الدفع', 'لم يتم الدفع']);
            $table->string('notes')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
