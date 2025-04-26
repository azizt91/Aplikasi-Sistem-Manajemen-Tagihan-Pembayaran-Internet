<?php

namespace App\Exports;

use App\Models\Tagihan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanExport implements FromCollection, WithHeadings
{
    protected $tanggal_awal;
    protected $tanggal_akhir;

    public function __construct($tanggal_awal, $tanggal_akhir)
    {
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
    }

    public function collection()
    {
        // Ambil data pemasukan (tagihan lunas)
        $pemasukan = Tagihan::join('pelanggan', 'pelanggan.id_pelanggan', '=', 'tagihan.id_pelanggan')
            ->where('tagihan.status', 'LS')
            ->whereBetween('tagihan.tgl_bayar', [$this->tanggal_awal, $this->tanggal_akhir])
            ->orderBy('tagihan.tgl_bayar', 'asc')
            ->get([
                'tagihan.tgl_bayar as tanggal',
                DB::raw('CONCAT("Pembayaran ", pelanggan.nama) as keterangan'),
                DB::raw('tagihan.tagihan as pemasukan'),
                DB::raw('NULL as pengeluaran')
            ]);

        // Ambil data pengeluaran
        $pengeluaran = Pengeluaran::whereBetween('tanggal', [$this->tanggal_awal, $this->tanggal_akhir])
            ->orderBy('tanggal', 'asc')
            ->get([
                'tanggal',
                'deskripsi as keterangan',
                DB::raw('NULL as pemasukan'),
                'jumlah as pengeluaran'
            ]);

        // Gabungkan data pemasukan dan pengeluaran
        $data = $pemasukan->concat($pengeluaran);

        // Hitung total pemasukan, pengeluaran, dan profit
        $totalPemasukan = $pemasukan->sum('pemasukan');
        $totalPengeluaran = $pengeluaran->sum('pengeluaran');
        $profit = $totalPemasukan - $totalPengeluaran;

        // Tambahkan baris total pemasukan dan pengeluaran
        $data->push([
            'tanggal' => 'Total',
            'keterangan' => '',
            'pemasukan' => $totalPemasukan,
            'pengeluaran' => $totalPengeluaran,
        ]);

        // Tambahkan baris profit
        $data->push([
            'tanggal' => 'Total Profit',
            'keterangan' => '',
            'pemasukan' => '',
            'pengeluaran' => $profit,
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Keterangan',
            'Pemasukan',
            'Pengeluaran',
        ];
    }
}
