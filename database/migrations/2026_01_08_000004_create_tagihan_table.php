<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel tagihan.
 * Menyimpan data tagihan bulanan pelanggan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 255)->nullable();
            $table->unsignedBigInteger('bulan');
            $table->year('tahun');
            $table->string('id_pelanggan', 16);
            $table->integer('tagihan');
            $table->integer('jumlah_dibayar')->default(0);
            $table->enum('status', ['BL', 'LS'])->default('BL'); // BL = Belum Lunas, LS = Lunas
            $table->date('tgl_bayar')->nullable();
            $table->enum('pembayaran_via', ['cash', 'transfer', 'qris', 'ewallet', 'online'])->default('cash');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('bulan')->references('id')->on('bulan')->onDelete('cascade');
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('cascade');
            
            // Indexes untuk optimasi query
            $table->index(['bulan', 'tahun', 'status']);
            $table->index(['id_pelanggan', 'status']);
            $table->index(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
