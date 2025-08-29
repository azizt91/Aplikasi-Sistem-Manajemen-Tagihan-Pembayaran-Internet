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
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('whatsapp');
            $table->enum('network_status', ['up', 'down', 'unknown'])->default('unknown')->after('status');
            $table->timestamp('last_seen')->nullable()->after('network_status');
            $table->text('mikrotik_notes')->nullable()->after('last_seen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'network_status', 'last_seen', 'mikrotik_notes']);
        });
    }
};
