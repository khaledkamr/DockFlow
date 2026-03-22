<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_inventory_id')->constrained('bulk_inventory')->cascadeOnDelete();
            $table->decimal('quantity_in', 15, 2);
            $table->decimal('quantity_remaining', 15, 2);
            $table->date('entry_date');
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_batches');
    }
};
