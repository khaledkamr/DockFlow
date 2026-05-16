<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_notes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code');
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->date('date')->nullable();
            $table->text('reason')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('tax', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->string('zatca_status')->nullable()->default('not sent');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_notes');
    }
};
