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
            $table->enum('status', ['متوفر', 'تم التسليم', 'متأخر', 'خدمات'])->default('متوفر');
            $table->string('received_by')->nullable();
            $table->string('delivered_by')->nullable();
            $table->string('location')->nullable();
            $table->string('notes')->nullable();
            $table->date('date')->nullable();
            $table->date('exit_date')->nullable();
            $table->foreignId('container_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
