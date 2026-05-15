<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zatca_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('company_group_id');
            $table->string('vat', 191)->nullable();
            $table->string('crn', 191)->nullable();
            $table->string('street', 191)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('sub_division', 191)->nullable();
            $table->string('building_no', 191)->nullable();
            $table->string('plot_no', 191)->nullable();
            $table->string('postal_code', 191)->nullable();
            $table->string('active_env', 191)->default('sim');
            $table->string('pro_request_id', 512)->nullable();
            $table->text('pro_private_key')->nullable();
            $table->string('pro_user_secret', 512)->nullable();
            $table->text('pro_user_name')->nullable();
            $table->text('pro_publickey')->nullable();
            $table->text('pro_cert')->nullable();
            $table->date('pro_cert_expire_date')->nullable();
            $table->unsignedBigInteger('pro_invoice_counter')->default(1);
            $table->string('pro_last_hash', 512)->default('NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==');
            $table->string('sim_request_id', 512)->nullable();
            $table->text('sim_private_key')->nullable();
            $table->string('sim_user_secret', 256)->nullable();
            $table->text('sim_user_name')->nullable();
            $table->text('sim_publickey')->nullable();
            $table->text('sim_cert')->nullable();
            $table->date('sim_cert_expire_date')->nullable();
            $table->unsignedBigInteger('sim_invoice_counter')->default(1);
            $table->string('sim_last_hash', 512)->default('NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zatca_companies');
    }
};
