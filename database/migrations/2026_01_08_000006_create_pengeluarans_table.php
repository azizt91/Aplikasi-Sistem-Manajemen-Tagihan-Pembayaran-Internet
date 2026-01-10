<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel pengeluarans.
 * Menyimpan data pengeluaran operasional.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->string('deskripsi', 255);
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->timestamps();
            
            // Index untuk optimasi query laporan
            $table->index(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
