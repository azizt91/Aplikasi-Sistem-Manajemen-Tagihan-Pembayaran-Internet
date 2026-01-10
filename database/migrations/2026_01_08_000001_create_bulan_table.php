<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel bulan.
 * Menyimpan daftar nama bulan dalam Bahasa Indonesia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulan', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulan');
    }
};
