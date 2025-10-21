<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_orders_containers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('container_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_orders_containers');
    }
};
