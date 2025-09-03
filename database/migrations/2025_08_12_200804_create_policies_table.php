<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('driver_name');
            $table->string('driver_NID');
            $table->string('driver_car');
            $table->string('car_code');
            $table->date('date');
            $table->string('code')->unique();
            $table->enum('type', ['تخزين', 'إستلام'])->default('تخزين');
            $table->string('storage_price')->nullable();
            $table->string('late_fee')->nullable();
            $table->enum('tax', ['غير معفي', 'معفي'])->default('غير معفي')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
