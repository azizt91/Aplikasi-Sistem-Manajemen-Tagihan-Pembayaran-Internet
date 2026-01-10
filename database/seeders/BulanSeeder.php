<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk tabel bulan.
 * Mengisi data bulan dalam Bahasa Indonesia.
 */
class BulanSeeder extends Seeder
{
    public function run(): void
    {
        $bulan = [
            ['bulan' => 'Januari'],
            ['bulan' => 'Februari'],
            ['bulan' => 'Maret'],
            ['bulan' => 'April'],
            ['bulan' => 'Mei'],
            ['bulan' => 'Juni'],
            ['bulan' => 'Juli'],
            ['bulan' => 'Agustus'],
            ['bulan' => 'September'],
            ['bulan' => 'Oktober'],
            ['bulan' => 'November'],
            ['bulan' => 'Desember'],
        ];

        foreach ($bulan as $b) {
            DB::table('bulan')->updateOrInsert(
                ['bulan' => $b['bulan']],
                ['bulan' => $b['bulan'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
