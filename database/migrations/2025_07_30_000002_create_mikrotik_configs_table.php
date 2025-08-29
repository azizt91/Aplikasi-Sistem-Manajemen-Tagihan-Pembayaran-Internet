<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mikrotik_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama konfigurasi MikroTik');
            $table->string('ip_address', 255)->comment('IP Address atau Hostname MikroTik');
            $table->integer('port')->default(8728)->comment('Port API MikroTik');
            $table->string('username')->comment('Username MikroTik');
            $table->text('password')->comment('Password MikroTik (encrypted)');
            $table->boolean('is_active')->default(false)->comment('Status aktif konfigurasi');
            $table->timestamp('last_connected')->nullable()->comment('Waktu terakhir terhubung');
            $table->enum('connection_status', ['connected', 'failed', 'error', 'never_tested'])->default('never_tested');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();
            
            $table->index(['ip_address', 'port']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_configs');
    }
};
