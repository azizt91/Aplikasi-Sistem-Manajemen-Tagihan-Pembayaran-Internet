<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel tripay_config.
 * Menyimpan konfigurasi payment gateway Tripay.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tripay_config', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->string('api_key', 255);
            $table->string('private_key', 255);
            $table->string('merchant_code', 255);
            $table->string('payment_channel_url', 255)
                ->default('https://tripay.co.id/api-sandbox/merchant/payment-channel');
            $table->string('transaction_create_url', 255)
                ->default('https://tripay.co.id/api-sandbox/transaction/create');
            $table->string('transaction_detail_url', 255)
                ->default('https://tripay.co.id/api-sandbox/transaction/detail');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tripay_config');
    }
};
