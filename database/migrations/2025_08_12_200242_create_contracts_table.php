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
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('company_representative');
            $table->string('company_representative_nationality');
            $table->string('company_representative_NID');
            $table->string('company_representative_role');
            $table->string('customer_representative');
            $table->string('customer_representative_nationality');
            $table->string('customer_representative_NID');
            $table->string('customer_representative_role');
            $table->string('service_one');
            $table->float('container_storage_price');
            $table->unsignedInteger('container_storage_period');
            $table->string('service_two');
            $table->float('move_container_price');
            $table->string('move_container_count');
            $table->string('service_three');
            $table->float('late_fee');
            $table->string('late_fee_period');
            $table->string('service_four');
            $table->float('exchange_container_price');
            $table->string('exchange_container_count');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
