<?php

namespace App\Exports;

use App\Models\Tagihan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

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
            ->get([
                'tagihan.tgl_bayar as tanggal',
                'tagihan.tagihan as jumlah',
                'pelanggan.nama',
                'tagihan.bulan',
                'tagihan.tahun'
            ])->map(function ($item) {
                // Format bulan dan tahun tagihan
                $bulan_tahun = Carbon::create($item->tahun, $item->bulan, 1)->translatedFormat('F Y');

                // Masukkan deskripsi pembayaran ke kolom keterangan
                $item->keterangan = "Pembayaran {$bulan_tahun} {$item->nama}";

                // Format angka dengan separator ribuan dan Rp
                $item->pemasukan = "Rp " . number_format($item->jumlah, 0, ',', '.');

                // Pastikan pengeluaran tetap kosong
                $item->pengeluaran = null;

                // Hapus kolom yang tidak diperlukan
                unset($item->nama, $item->bulan, $item->tahun, $item->jumlah);

                return $item;
            });

        // Ambil data pengeluaran
        $pengeluaran = Pengeluaran::whereBetween('tanggal', [$this->tanggal_awal, $this->tanggal_akhir])
            ->get([
                'tanggal',
                'deskripsi as keterangan',
                DB::raw('NULL as pemasukan'),
                'jumlah as pengeluaran'
            ])->map(function ($item) {
                // Format angka pengeluaran dengan Rp dan separator ribuan
                $item->pengeluaran = "Rp " . number_format($item->pengeluaran, 0, ',', '.');
                return $item;
            });

        // Gabungkan data dan urutkan berdasarkan tanggal
        $data = $pemasukan->concat($pengeluaran)->sortBy('tanggal')->values();

        // Hitung total pemasukan, pengeluaran, dan profit
        $totalPemasukan = $pemasukan->sum(fn($item) => (int)str_replace(['Rp ', '.'], '', $item->pemasukan));
        $totalPengeluaran = $pengeluaran->sum(fn($item) => (int)str_replace(['Rp ', '.'], '', $item->pengeluaran));
        $profit = $totalPemasukan - $totalPengeluaran;

        // Tambahkan baris total pemasukan dan pengeluaran dengan format Rp
        $data->push([
            'tanggal' => 'Total',
            'keterangan' => '',
            'pemasukan' => "Rp " . number_format($totalPemasukan, 0, ',', '.'),
            'pengeluaran' => "Rp " . number_format($totalPengeluaran, 0, ',', '.'),
        ]);

        // Tambahkan baris profit dengan format Rp
        $data->push([
            'tanggal' => 'Total Profit',
            'keterangan' => '',
            'pemasukan' => '',
            'pengeluaran' => "Rp " . number_format($profit, 0, ',', '.'),
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
