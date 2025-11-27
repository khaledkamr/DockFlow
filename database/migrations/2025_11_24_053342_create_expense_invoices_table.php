<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code');
            $table->date('date');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('supplier_invoice_number')->nullable();
            $table->string('payment_method');
            $table->decimal('amount_before_tax', 15, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('tax', 15, 2);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->boolean('is_posted')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_invoices');
    }
};
