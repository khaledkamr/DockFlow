<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->enum('type', ['سند قبض نقدي', 'سند قبض بشيك', 'سند صرف نقدي', 'سند صرف بشيك']);
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->string('hatching')->nullable();
            $table->string('description');
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_posted')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
