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
            $table->string('country')->nullable();              // الدولة
            $table->string('city');                             // المدينة
            $table->string('street')->nullable();               // الشارع   
            $table->string('district')->nullable();             // الحي
            $table->string('building_number')->nullable();      // رقم المبنى
            $table->string('secondary_number')->nullable();     // الرقم الفرعي
            $table->string('postal_code')->nullable();          // الرمز البريدي
            $table->string('short_address')->nullable();        // العنوان المختصر
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_addresses');
    }
};
