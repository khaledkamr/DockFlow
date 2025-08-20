<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('Representative');
            $table->string('Representative_NID');
            $table->string('Representative_nationality');
            $table->float('container_storage_price');
            $table->unsignedInteger('container_storage_period');
            $table->float('move_container_price');
            $table->float('late_fee');
            $table->float('exchange_container_price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
