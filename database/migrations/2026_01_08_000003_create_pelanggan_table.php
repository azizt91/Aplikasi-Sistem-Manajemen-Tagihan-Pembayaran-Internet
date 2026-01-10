<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel pelanggan.
 * Menyimpan data pelanggan internet lengkap dengan koordinat dan status jaringan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->string('id_pelanggan', 16)->primary();
            $table->string('nama', 255);
            $table->text('alamat');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('house_image', 255)->nullable();
            $table->string('whatsapp', 15);
            $table->string('ip_address', 255)->nullable();
            $table->string('email', 30)->unique();
            $table->string('password', 255);
            $table->string('level', 5);
            $table->string('id_paket', 6);
            $table->date('tanggal_cabut')->nullable();
            $table->string('profile_picture', 255)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->enum('network_status', ['up', 'down', 'unknown'])->default('unknown');
            $table->timestamp('last_seen')->nullable();
            $table->text('mikrotik_notes')->nullable();
            $table->date('tanggal_pasang')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('id_paket')->references('id_paket')->on('paket')->onDelete('cascade');
            
            // Index untuk optimasi query
            $table->index('status');
            $table->index('network_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
