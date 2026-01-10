<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Database Seeder utama.
 * Menjalankan semua seeder yang diperlukan untuk setup awal aplikasi.
 * 
 * Cara menjalankan:
 * php artisan db:seed
 * 
 * Atau untuk fresh install:
 * php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BulanSeeder::class,
            PaketSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            FonnteNotificationSettingSeeder::class,
            TripayConfigSeeder::class,
        ]);
    }
}
