<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zatca_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->uuid('invoice_uuid');
            $table->string('invoice_hash', 512)->nullable();
            $table->string('pre_hash', 512)->nullable();
            $table->longText('request_xml')->nullable();
            $table->longText('encoded_xml')->nullable();
            $table->string('qr_data', 4000)->nullable();
            $table->longText('response_log')->nullable();
            $table->dateTime('request_date')->nullable();
            $table->string('status', 191)->nullable();
            $table->decimal('invoice_amount', 12, 4)->nullable();
            $table->decimal('invoice_vat_amount', 12, 4)->nullable();
            $table->decimal('invoice_total', 12, 4)->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->decimal('diff_invoice_amount', 12, 4)->nullable();
            $table->decimal('diff_invoice_vat_amount', 12, 4)->nullable();
            $table->decimal('diff_invoice_total', 12, 4)->nullable();
            $table->boolean('diff_status')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zatca_invoices');
    }
};
