<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk tabel users.
 * Membuat akun admin default.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'level' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
