<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel fonnte.
 * Menyimpan token konfigurasi Fonnte (WhatsApp gateway).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fonnte', function (Blueprint $table) {
            $table->id();
            $table->string('token', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fonnte');
    }
};
