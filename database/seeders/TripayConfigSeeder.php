<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk tabel tripay_config.
 * Mengisi konfigurasi default Tripay (sandbox).
 */
class TripayConfigSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tripay_config')->updateOrInsert(
            ['id' => 1],
            [
                'is_enabled' => true,
                'api_key' => 'YOUR_API_KEY',
                'private_key' => 'YOUR_PRIVATE_KEY',
                'merchant_code' => 'YOUR_MERCHANT_CODE',
                'payment_channel_url' => 'https://tripay.co.id/api-sandbox/merchant/payment-channel',
                'transaction_create_url' => 'https://tripay.co.id/api-sandbox/transaction/create',
                'transaction_detail_url' => 'https://tripay.co.id/api-sandbox/transaction/detail',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
