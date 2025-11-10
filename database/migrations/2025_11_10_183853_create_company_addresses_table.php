<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type')->nullable()->default('الرئيسي');
            $table->string('country')->nullable();
            $table->string('city');
            $table->string('street')->nullable();
            $table->string('district')->nullable();
            $table->string('building_number')->nullable();
            $table->string('secondary_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('short_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_addresses');
    }
};
