<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel users.
 * Menyimpan data pengguna sistem (admin).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('profile_picture', 255)->nullable();
            $table->string('level', 255)->default('Admin');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
