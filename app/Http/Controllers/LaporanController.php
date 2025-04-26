<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pengeluaran;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB; // Mengimpor DB untuk menggunakan DB::raw
use Maatwebsite\Excel\Facades\Excel; // Impor untuk eksport ke excel
use App\Exports\LaporanExport;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        // Mengambil pemasukan (tagihan lunas)
        $pemasukan = Tagihan::join('pelanggan', 'pelanggan.id_pelanggan', '=', 'tagihan.id_pelanggan')
            ->where('tagihan.status', 'LS')
            ->whereBetween('tagihan.tgl_bayar', [$tanggal_awal, $tanggal_akhir])  // Pastikan filter tanggal diterapkan dengan benar
            ->orderBy('tagihan.tgl_bayar', 'asc')
            ->get([
                'tagihan.tgl_bayar as tanggal',
                DB::raw('CONCAT("Pembayaran ", pelanggan.nama) as keterangan'),
                'tagihan.tagihan as jumlah'  // Menggunakan kolom 'tagihan' untuk jumlah
            ]);

        // Menambahkan nomor urut secara manual
        $pemasukan = $pemasukan->map(function ($item, $index) {
            $item->nomor = $index + 1;  // Menambahkan nomor urut dimulai dari 1
            return $item;
        });

        // Mengambil pengeluaran
        $pengeluaran = Pengeluaran::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->orderBy('tanggal', 'asc')
            ->get([
                'tanggal',
                'deskripsi as keterangan',
                'jumlah'
            ]);

        // Gabungkan data pemasukan dan pengeluaran
        $data = $pemasukan->map(function ($item) {
            $item->tipe = 'Pemasukan';
            return $item;
        })->concat($pengeluaran->map(function ($item) {
            $item->tipe = 'Pengeluaran';
            return $item;
        }));

        // Hitung total pemasukan, pengeluaran, dan profit
        $totalPemasukan = $pemasukan->sum('jumlah');
        $totalPengeluaran = $pengeluaran->sum('jumlah');
        $profit = $totalPemasukan - $totalPengeluaran;

        return view('laporan.index', compact('data', 'tanggal_awal', 'tanggal_akhir', 'totalPemasukan', 'totalPengeluaran', 'profit'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        // Format nama file dengan tanggal awal dan akhir
        $tanggal_awal = \Carbon\Carbon::parse($request->tanggal_awal)->format('dmY');
        $tanggal_akhir = \Carbon\Carbon::parse($request->tanggal_akhir)->format('dmY');
        $fileName = "laporan_keuangan_{$tanggal_awal}-{$tanggal_akhir}.xlsx";

        return Excel::download(new LaporanExport($request->tanggal_awal, $request->tanggal_akhir), $fileName);
    }
}




