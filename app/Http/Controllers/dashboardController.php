<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class dashboardController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Ambil jumlah paket dari model Paket
    //     $jumlah_paket = Paket::count();
    //     $user = DB::table('users')->count();
    //     $jumlah_pelanggan_aktif = Pelanggan::where('status', 'aktif')->count();

    //     $jumlah_pelanggan_nonaktif = Pelanggan::where('status', 'nonaktif')->count();

    //     $tagihanBulanIni = Tagihan::where('status', 'LS')
    //     ->where('tahun', Carbon::now()->year)
    //     ->where('bulan', Carbon::now()->month)
    //     ->sum('tagihan');

    //     $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

    //     // Tentukan tahun dan bulan saat ini
    //     $now = Carbon::now();
    //     $year = $now->year;
    //     $month = $now->month;

    //     // Hitung jumlah pelanggan lunas dengan status aktif
    //     $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
    //     ->whereHas('tagihan', function ($query) use ($year, $month) {
    //         $query->where('status', 'LS')
    //         ->where('tahun', $year)
    //         ->where('bulan', $month);
    //     })->count();

    //     // Hitung jumlah pelanggan belum lunas dengan status aktif
    //     $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
    //     ->whereHas('tagihan', function ($query) use ($year, $month) {
    //         $query->where(function ($query) use ($year, $month) {
    //             $query->where('status', '!=', 'LS')
    //             ->orWhereNull('status');
    //         })->where('tahun', $year)
    //         ->where('bulan', $month);
    //     })->count();

    //     // Logika untuk pengambilan pendapatan berdasarkan bulan dan tahun yang dipilih
    //     $selectedMonth = $request->input('bulan', $month); // Gunakan bulan saat ini sebagai default
    //     $selectedYear = $request->input('tahun', $year);   // Gunakan tahun saat ini sebagai default

    //     $totalRevenue = Tagihan::where('tahun', $selectedYear)
    //     ->where('bulan', $selectedMonth)
    //     ->where('status', 'LS')
    //     ->sum('tagihan');

    //     // Query untuk menghitung pengeluaran bulan ini
    //     $pengeluaranBulanIni = Pengeluaran::where('tahun', $selectedYear)
    //     ->where('bulan', $selectedMonth)
    //     ->sum('jumlah');

    //     // Hitung total pendapatan setelah dikurangi pengeluaran
    //     $netRevenue = $totalRevenue - $pengeluaranBulanIni;

    //     // Mendapatkan data pendapatan dan pengeluaran setiap bulan
    //     $pendapatan = [];
    //     $pengeluaran = [];

    //     for ($month = 1; $month <= 12; $month++) {
    //         $pendapatan[] = Tagihan::where('tahun', $selectedYear)
    //         ->where('bulan', $month)
    //         ->where('status', 'LS')
    //         ->sum('tagihan');

    //         $pengeluaran[] = Pengeluaran::where('tahun', $selectedYear)
    //         ->where('bulan', $month)
    //         ->sum('jumlah');
    //     }

    //     if ($request->ajax()) {
    //         // Kembalikan data dalam format JSON jika request adalah AJAX
    //         return response()->json([
    //             'netRevenue' => $netRevenue,
    //             'totalRevenue' => $totalRevenue,
    //             'pengeluaranBulanIni' => $pengeluaranBulanIni,
    //             'pendapatan' => $pendapatan,
    //             'pengeluaran' => $pengeluaran
    //         ]);
    //     }

    //     return view('home.index', compact(
    //         'user',
    //         'jumlah_paket',
    //         'tagihanBulanIni',
    //         'jumlah_pelanggan_lunas',
    //         'jumlah_pelanggan_belum_lunas',
    //         'jumlah_pelanggan_aktif',
    //         'jumlah_pelanggan_nonaktif',
    //         'totalRevenue',
    //         'selectedMonth',
    //         'selectedYear',
    //         'pengeluaranBulanIni',
    //         'netRevenue',
    //         'pendapatan',
    //         'pengeluaran'
    //     ));
    // }

    public function index(Request $request)
    {
        // Ambil jumlah paket dari model Paket
        $jumlah_paket = Paket::count();
        $user = DB::table('users')->count();
        $jumlah_pelanggan_aktif = Pelanggan::where('status', 'aktif')->count();
        $jumlah_pelanggan_nonaktif = Pelanggan::where('status', 'nonaktif')->count();

        // Mengambil nilai bulan dan tahun yang dipilih (default ke bulan dan tahun saat ini)
        $selectedMonth = $request->input('bulan', Carbon::now()->month); // Filter berdasarkan bulan yang dipilih
        $selectedYear = $request->input('tahun', Carbon::now()->year);   // Filter berdasarkan tahun yang dipilih

        // Mengambil tagihan bulan dan tahun yang dipilih
        $tagihanBulanIni = Tagihan::where('status', 'LS')
            ->where('tahun', $selectedYear)
            ->where('bulan', $selectedMonth)
            ->sum('tagihan');

        // Ambil ID pelanggan yang aktif
        $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

        // Hitung jumlah pelanggan lunas berdasarkan bulan dan tahun yang dipilih
        $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
            ->whereHas('tagihan', function ($query) use ($selectedYear, $selectedMonth) {
                $query->where('status', 'LS')
                    ->where('tahun', $selectedYear)
                    ->where('bulan', $selectedMonth);
            })->count();

        // Hitung jumlah pelanggan belum lunas berdasarkan bulan dan tahun yang dipilih
        $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
            ->whereHas('tagihan', function ($query) use ($selectedYear, $selectedMonth) {
                $query->where(function ($query) use ($selectedYear, $selectedMonth) {
                    $query->where('status', '!=', 'LS')
                        ->orWhereNull('status');
                })->where('tahun', $selectedYear)
                ->where('bulan', $selectedMonth);
            })->count();

        // Query untuk menghitung total pendapatan dan pengeluaran
        $totalRevenue = Tagihan::where('tahun', $selectedYear)
            ->where('bulan', $selectedMonth)
            ->where('status', 'LS')
            ->sum('tagihan');

        $pengeluaranBulanIni = Pengeluaran::where('tahun', $selectedYear)
            ->where('bulan', $selectedMonth)
            ->sum('jumlah');

        // Hitung total pendapatan setelah dikurangi pengeluaran
        $netRevenue = $totalRevenue - $pengeluaranBulanIni;

        // Mendapatkan data pendapatan dan pengeluaran untuk grafik
        $pendapatan = [];
        $pengeluaran = [];
        for ($month = 1; $month <= 12; $month++) {
            $pendapatan[] = Tagihan::where('tahun', $selectedYear)
                ->where('bulan', $month)
                ->where('status', 'LS')
                ->sum('tagihan');

            $pengeluaran[] = Pengeluaran::where('tahun', $selectedYear)
                ->where('bulan', $month)
                ->sum('jumlah');
        }

        if ($request->ajax()) {
            // Kembalikan data dalam format JSON jika request adalah AJAX
            return response()->json([
                'netRevenue' => $netRevenue,
                'totalRevenue' => $totalRevenue,
                'pengeluaranBulanIni' => $pengeluaranBulanIni,
                'pendapatan' => $pendapatan,
                'pengeluaran' => $pengeluaran,
                'jumlah_pelanggan_lunas' => $jumlah_pelanggan_lunas,  // Data jumlah lunas
                'jumlah_pelanggan_belum_lunas' => $jumlah_pelanggan_belum_lunas, // Data jumlah belum lunas
            ]);
        }

        return view('home.index', compact(
            'user',
            'jumlah_paket',
            'tagihanBulanIni',
            'jumlah_pelanggan_lunas',
            'jumlah_pelanggan_belum_lunas',
            'jumlah_pelanggan_aktif',
            'jumlah_pelanggan_nonaktif',
            'totalRevenue',
            'selectedMonth',
            'selectedYear',
            'pengeluaranBulanIni',
            'netRevenue',
            'pendapatan',
            'pengeluaran'
        ));
    }


    public function login2(){

        return view('Auth.login2');
    }
    public function register2(){

        return view('Auth.register2');
    }

    // public function updateData(Request $request)
    // {
    //     $selectedMonth = $request->query('bulan');
    //     $selectedYear = $request->query('tahun');

    //     $totalRevenue = Tagihan::where('tahun', $selectedYear)
    //         ->where('bulan', $selectedMonth)
    //         ->where('status', 'LS')
    //         ->sum('tagihan');

    //     $pengeluaranBulanIni = Pengeluaran::where('tahun', $selectedYear)
    //         ->where('bulan', $selectedMonth)
    //         ->sum('jumlah');

    //     // Pastikan nilai pengeluaranBulanIni adalah integer
    //     $pengeluaranBulanIni = (int) $pengeluaranBulanIni;

    //     $netRevenue = $totalRevenue - $pengeluaranBulanIni;

    //     return response()->json([
    //         'netRevenue' => (int) $netRevenue,
    //         'totalRevenue' => (int) $totalRevenue,
    //         'pengeluaranBulanIni' => $pengeluaranBulanIni,
    //     ]);
    // }

    public function updateData(Request $request)
    {
        $selectedMonth = $request->query('bulan');
        $selectedYear = $request->query('tahun');

        // Mengambil total pendapatan untuk bulan dan tahun yang dipilih
        $totalRevenue = Tagihan::where('tahun', $selectedYear)
            ->where('bulan', $selectedMonth)
            ->where('status', 'LS')
            ->sum('tagihan');

        // Mengambil total pengeluaran untuk bulan dan tahun yang dipilih
        $pengeluaranBulanIni = Pengeluaran::where('tahun', $selectedYear)
            ->where('bulan', $selectedMonth)
            ->sum('jumlah');

        // Pastikan nilai pengeluaranBulanIni adalah integer
        $pengeluaranBulanIni = (int) $pengeluaranBulanIni;

        // Hitung net revenue
        $netRevenue = $totalRevenue - $pengeluaranBulanIni;

        // Ambil ID pelanggan yang aktif
        $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

        // Hitung jumlah pelanggan lunas berdasarkan bulan dan tahun yang dipilih
        $jumlah_pelanggan_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
            ->whereHas('tagihan', function ($query) use ($selectedYear, $selectedMonth) {
                $query->where('status', 'LS')
                    ->where('tahun', $selectedYear)
                    ->where('bulan', $selectedMonth);
            })->count();

        // Hitung jumlah pelanggan belum lunas berdasarkan bulan dan tahun yang dipilih
        $jumlah_pelanggan_belum_lunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
            ->whereHas('tagihan', function ($query) use ($selectedYear, $selectedMonth) {
                $query->where(function ($query) use ($selectedYear, $selectedMonth) {
                    $query->where('status', '!=', 'LS')
                        ->orWhereNull('status');
                })->where('tahun', $selectedYear)
                ->where('bulan', $selectedMonth);
            })->count();

        // Mengembalikan data dalam format JSON
        return response()->json([
            'netRevenue' => (int) $netRevenue,
            'totalRevenue' => (int) $totalRevenue,
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
            'jumlah_pelanggan_lunas' => $jumlah_pelanggan_lunas, // Data jumlah lunas
            'jumlah_pelanggan_belum_lunas' => $jumlah_pelanggan_belum_lunas, // Data jumlah belum lunas
        ]);
    }


    public function getDataChart(Request $request)
    {
        $selectedYear = $request->query('tahun');

        // Mengambil data pendapatan dan pengeluaran setiap bulan berdasarkan tahun yang dipilih
        $pendapatan = [];
        $pengeluaran = [];

        for ($month = 1; $month <= 12; $month++) {
            $pendapatan[] = Tagihan::where('tahun', $selectedYear)
                ->where('bulan', $month)
                ->where('status', 'LS')
                ->sum('tagihan');

            $pengeluaran[] = Pengeluaran::where('tahun', $selectedYear)
                ->where('bulan', $month)
                ->sum('jumlah');
        }

        return response()->json([
            'pendapatan' => $pendapatan,
            'pengeluaran' => $pengeluaran
        ]);
    }



}




