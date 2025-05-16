<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pengeluaran;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB; // Mengimpor DB untuk menggunakan DB::raw
use Maatwebsite\Excel\Facades\Excel; // Impor untuk eksport ke excel
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Dompdf\Dompdf;

class LaporanController extends Controller
{

    public function index(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        // Mengambil pemasukan (tagihan lunas)
        $pemasukan = Tagihan::join('pelanggan', 'pelanggan.id_pelanggan', '=', 'tagihan.id_pelanggan')
            ->where('tagihan.status', 'LS')
            ->whereBetween('tagihan.tgl_bayar', [$tanggal_awal, $tanggal_akhir])
            ->get([
                'tagihan.tgl_bayar as tanggal',
                'pelanggan.nama',
                'tagihan.bulan',  // Ambil bulan dari database
                'tagihan.tahun',  // Ambil tahun dari database
                'tagihan.tagihan as jumlah'
            ])->map(function ($item) {
                // Format bulan dan tahun langsung dari kolom bulan & tahun
                $bulan_tahun = Carbon::create($item->tahun, $item->bulan, 1)->translatedFormat('F Y');

                // Perbaiki keterangan dengan bulan dan tahun
                $item->keterangan = "Pembayaran {$bulan_tahun} {$item->nama}";
                $item->tipe = 'Pemasukan';
                return $item;
            });

        // Mengambil pengeluaran
        $pengeluaran = Pengeluaran::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->get([
                'tanggal',
                'deskripsi as keterangan',
                'jumlah'
            ])->map(function ($item) {
                $item->tipe = 'Pengeluaran';
                return $item;
            });

        // Gabungkan dan urutkan berdasarkan tanggal
        $data = $pemasukan->concat($pengeluaran)->sortBy('tanggal')->values();

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

    public function exportPdf(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        // Ambil data laporan keuangan
        $pemasukan = Tagihan::join('pelanggan', 'pelanggan.id_pelanggan', '=', 'tagihan.id_pelanggan')
            ->where('tagihan.status', 'LS')
            ->whereBetween('tagihan.tgl_bayar', [$tanggal_awal, $tanggal_akhir])
            ->get(['tagihan.tgl_bayar as tanggal', 'pelanggan.nama', 'tagihan.tagihan as jumlah'])
            ->map(function ($item) {
                $item->tipe = 'Pemasukan';
                return $item;
            });

        $pengeluaran = Pengeluaran::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->get(['tanggal', 'deskripsi as keterangan', 'jumlah'])
            ->map(function ($item) {
                $item->tipe = 'Pengeluaran';
                return $item;
            });

        $data = $pemasukan->concat($pengeluaran)->sortBy('tanggal')->values();
        $totalPemasukan = $pemasukan->sum('jumlah');
        $totalPengeluaran = $pengeluaran->sum('jumlah');
        $profit = $totalPemasukan - $totalPengeluaran;

        // Render view to HTML
        $html = View::make('laporan.pdf', compact('data', 'tanggal_awal', 'tanggal_akhir', 'totalPemasukan', 'totalPengeluaran', 'profit'))->render();

        // Buat objek Dompdf
        $dompdf = new Dompdf();

        // Set base path untuk DOMPDF
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        // Load HTML content
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Tampilkan PDF dengan memberikan nama file pada saat streaming
        return $dompdf->stream("laporan_keuangan_{$tanggal_awal}-{$tanggal_akhir}.pdf");
    }
}









