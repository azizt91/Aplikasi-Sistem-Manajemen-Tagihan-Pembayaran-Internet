<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk tabel fonnte_notification_settings.
 * Mengisi template pesan notifikasi default.
 */
class FonnteNotificationSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaultMessage = "*Informasi Tagihan WiFi Anda*\r\n\r\n" .
            "Hai Bapak/Ibu @{{nama}}\r\n" .
            "ID Pelanggan @{{id_pelanggan}}\r\n\r\n" .
            "Informasi tagihan Bapak/Ibu bulan ini adalah:\r\n" .
            "Jumlah Tagihan: *Rp@{{tagihan}}*\r\n" .
            "Periode Tagihan: *@{{periode}}*\r\n\r\n" .
            "Terima kasih atas kepercayaan Anda menggunakan layanan kami.\r\n" .
            "_____________________________\r\n" .
            "*Ini adalah pesan otomatis, jika telah membayar tagihan, abaikan pesan ini*";

        DB::table('fonnte_notification_settings')->updateOrInsert(
            ['id' => 1],
            [
                'is_active' => false,
                'send_date_option' => 'tanggal_pasang',
                'custom_message' => $defaultMessage,
            ]
        );
    }
}
