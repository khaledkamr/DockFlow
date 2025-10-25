<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code');
            $table->string('status');    // e.g., في الساحة, في الإنتظار, متأخر
            $table->string('condition')->nullable();
            $table->string('received_by')->nullable();
            $table->string('delivered_by')->nullable();
            $table->string('location')->nullable();
            $table->string('notes')->nullable();
            $table->date('date')->nullable();
            $table->date('exit_date')->nullable();
            $table->foreignId('container_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
