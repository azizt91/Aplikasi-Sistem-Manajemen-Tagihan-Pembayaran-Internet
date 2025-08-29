<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to include 'disconnected' status
        DB::statement("ALTER TABLE mikrotik_configs MODIFY COLUMN connection_status ENUM('connected', 'failed', 'error', 'never_tested', 'disconnected') DEFAULT 'never_tested'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum to original values
        DB::statement("ALTER TABLE mikrotik_configs MODIFY COLUMN connection_status ENUM('connected', 'failed', 'error', 'never_tested') DEFAULT 'never_tested'");
    }
};
