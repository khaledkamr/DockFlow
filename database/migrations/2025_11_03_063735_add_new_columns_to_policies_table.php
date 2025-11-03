<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->decimal('storage_price')->default(0)->after('date');
            $table->decimal('storage_duration')->default(0)->after('storage_price');
            $table->decimal('late_fee')->default(0)->after('storage_duration');
        });
    }

    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn(['storage_price', 'storage_duration', 'late_fee']);
        });
    }
};
