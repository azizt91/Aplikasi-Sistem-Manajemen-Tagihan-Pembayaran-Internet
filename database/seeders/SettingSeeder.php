<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk tabel settings.
 * Mengisi pengaturan default aplikasi.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'favicon', 'value' => null],
            ['key' => 'logo_admin', 'value' => null],
            ['key' => 'logo_pelanggan', 'value' => null],
            ['key' => 'sidebar_logo', 'value' => null],
            ['key' => 'receipt_logo', 'value' => null],
            ['key' => 'sidebar_text', 'value' => 'Billing Internet'],
            ['key' => 'company_address', 'value' => 'Alamat Perusahaan'],
            ['key' => 'whatsapp_number', 'value' => '081234567890'],
            ['key' => 'pwa_name', 'value' => 'Billing Internet'],
            ['key' => 'pwa_short_name', 'value' => 'Billing'],
            ['key' => 'pwa_description', 'value' => 'Sistem Manajemen Tagihan Pembayaran Internet'],
            ['key' => 'pwa_logo', 'value' => null],
            ['key' => 'app_name_admin', 'value' => 'Billing Internet'],
            ['key' => 'app_name_pelanggan', 'value' => 'Billing Internet'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
