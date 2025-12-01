<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');

            $table->foreignId('cost_center_id')->nullable()->constrained()->nullOnDelete()->after('vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete()->after('vehicle_id');
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn('cost_center_id');
        });
    }
};
