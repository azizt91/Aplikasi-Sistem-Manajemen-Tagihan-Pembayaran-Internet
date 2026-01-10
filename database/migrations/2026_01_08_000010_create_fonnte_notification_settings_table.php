<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel fonnte_notification_settings.
 * Menyimpan pengaturan notifikasi WhatsApp otomatis.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fonnte_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->text('custom_message')->nullable();
            $table->integer('delay_seconds')->default(10); // Delay between messages in seconds
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fonnte_notification_settings');
    }
};
