<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Pelanggan;
use App\Models\Paket;
use Carbon\Carbon;
use Illuminate\Support\Str;



    class PelangganImport implements ToModel, WithHeadingRow
    {
        /**
        * @param array $row
        *
        * @return \Illuminate\Database\Eloquent\Model|null
        */

        public function model(array $row)
        {


            if (empty(trim($row['nama']))) {
                return null; // Lewati baris jika nama kosong
            }

            $whatsapp = trim($row['whatsapp']); // Bersihkan spasi ekstra

            if (!Str::startsWith($whatsapp, '62')) {
                $whatsapp = '62' . ltrim($whatsapp, '0'); // Pastikan selalu diawali dengan 62
            }

            $lastPelanggan = Pelanggan::latest('id_pelanggan')->first(); // Ambil pelanggan terakhir
            $newIdNumber = $lastPelanggan ? intval(substr($lastPelanggan->id_pelanggan, 1)) + 1 : 1; // Ambil angka terakhir dan tambah 1
            $newIdPelanggan = 'C' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT); // Buat format C001, C002, dst.

            $paket = Paket::where('id_paket', $row['paket'])->first();

            return new Pelanggan([
                'id_pelanggan' => !empty($row['id_pelanggan']) ? $row['id_pelanggan'] : $newIdPelanggan, // Gunakan ID dari file, atau buat otomatis jika kosong
                'nama' => substr(trim($row['nama']), 0, 20), // Batasi panjang nama agar sesuai dengan database
                'alamat' => !empty(trim($row['alamat'])) ? trim($row['alamat']) : 'Tanpa Alamat',
                'whatsapp' => !empty(trim($row['whatsapp'])) ? substr(trim($row['whatsapp']), 0, 20) : '0000000000', // Beri default jika kosong
                'email' => !empty(trim($row['email'])) ? trim($row['email']) : 'no-email@example.com',
                'password' => !empty(trim($row['password'])) ? trim($row['password']) : '12345678',
                'status' => strtolower($row['status']) == 'aktif' ? 'aktif' : 'nonaktif',
                'jatuh_tempo' => $row['jatuh_tempo'],
                'tanggal_pasang' => is_numeric($row['tanggal_pasang'])
                        ? Carbon::createFromFormat('Y-m-d', Carbon::createFromTimestamp(((int)$row['tanggal_pasang'] - 25569) * 86400)->format('Y-m-d'))
                        : Carbon::createFromFormat('Y-m-d', trim($row['tanggal_pasang']))->format('Y-m-d'),
                'level' => 'User',
                'id_paket' => $paket ? $paket->id_paket : 'P004',
            ]);
        }
    }
