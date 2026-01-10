<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk tabel paket.
 * Mengisi data paket internet contoh.
 */
class PaketSeeder extends Seeder
{
    public function run(): void
    {
        $paket = [
            ['id_paket' => 'P001', 'paket' => '20 Mbps', 'tarif' => 250000],
            ['id_paket' => 'P002', 'paket' => '10 Mbps', 'tarif' => 200000],
            ['id_paket' => 'P003', 'paket' => '8 Mbps', 'tarif' => 180000],
            ['id_paket' => 'P004', 'paket' => '5 Mbps', 'tarif' => 150000],
            ['id_paket' => 'P005', 'paket' => '3 Mbps', 'tarif' => 100000],
            ['id_paket' => 'P006', 'paket' => '1.5 MB', 'tarif' => 50000],
        ];

        foreach ($paket as $p) {
            DB::table('paket')->updateOrInsert(
                ['id_paket' => $p['id_paket']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
