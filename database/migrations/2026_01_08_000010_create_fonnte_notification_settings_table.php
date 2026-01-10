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
            $table->boolean('is_active')->default(false);
            $table->string('send_date_option', 255)->default('tanggal_pasang');
            $table->text('custom_message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fonnte_notification_settings');
    }
};
