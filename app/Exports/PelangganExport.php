<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class PelangganExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Pelanggan::query()->select('id_pelanggan', 'nama', 'alamat', 'whatsapp', 'email', 'password', 'id_paket', 'status', 'jatuh_tempo', 'tanggal_pasang')->with('paket');;
    }

    public function map($pelanggan): array
    {
        return [
            $pelanggan->id_pelanggan,
            $pelanggan->nama,
            $pelanggan->alamat,
            "'" . $pelanggan->whatsapp, // Tambahkan tanda petik agar format WhatsApp tetap sesuai
            $pelanggan->email,
            $pelanggan->password,
            optional($pelanggan->paket)->paket,
            $pelanggan->status,
            $pelanggan->jatuh_tempo,
            \Carbon\Carbon::parse($pelanggan->tanggal_pasang)->translatedFormat('d F Y'), // Format tanggal agar lebih mudah dibaca

        ];
    }

    public function headings(): array
    {
        return [
            'ID Pelanggan',
            'Nama',
            'Alamat',
            'WhatsApp',
            'Email',
            'Password',
            'Paket',
            'Status',
            'Jatuh Tempo',
            'Tanggal Pasang'

        ];
    }
}
