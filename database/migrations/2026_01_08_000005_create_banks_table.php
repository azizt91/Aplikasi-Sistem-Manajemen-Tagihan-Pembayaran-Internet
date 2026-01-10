<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel banks.
 * Menyimpan data rekening bank/e-wallet untuk pembayaran.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank', 255);
            $table->string('pemilik_rekening', 255);
            $table->string('nomor_rekening', 255);
            $table->string('url_icon', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
