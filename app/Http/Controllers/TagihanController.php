<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagihanRequest;
use App\Models\Bulan;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Services\TagihanService;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Dompdf\Dompdf;

/**
 * Controller untuk menangani operasi tagihan.
 * 
 * Menggunakan TagihanService untuk business logic dan
 * Form Request untuk validasi.
 */
class TagihanController extends Controller
{
    protected TagihanService $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    /**
     * Tampilkan halaman index tagihan.
     */
    public function index()
    {
        $bulanList = Bulan::all();
        $jumlahPelangganAktif = Pelanggan::where('status', 'aktif')->count();

        return view('tagihan.index', compact('bulanList', 'jumlahPelangganAktif'));
    }

    /**
     * Simpan tagihan baru.
     * 
     * Menggunakan StoreTagihanRequest untuk validasi dan
     * TagihanService untuk business logic.
     * Otomatis membuat tagihan untuk semua pelanggan aktif.
     */
    public function storeTagihan(Request $request)
    {
        // Validasi hanya bulan dan tahun
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
        ], [
            'bulan.required' => 'Bulan harus dipilih',
            'tahun.required' => 'Tahun harus dipilih',
        ]);

        try {
            // Ambil semua ID pelanggan aktif
            $pelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan')->toArray();
            
            if (empty($pelangganIds)) {
                Alert::warning('Peringatan', 'Tidak ada pelanggan aktif');
                return redirect()->route('tagihan');
            }

            $result = $this->tagihanService->generateBillsForCustomers(
                $request->bulan,
                $request->tahun,
                $pelangganIds
            );

            if ($result['created'] > 0) {
                Alert::success('Sukses', "Berhasil membuat {$result['created']} tagihan");
            } elseif ($result['skipped'] > 0) {
                Alert::info('Info', 'Semua tagihan sudah ada sebelumnya');
            } else {
                Alert::warning('Peringatan', 'Tidak ada tagihan yang dibuat');
            }

            if (!empty($result['errors'])) {
                Log::warning('Beberapa tagihan gagal dibuat', ['errors' => $result['errors']]);
            }
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan tagihan', ['error' => $e->getMessage()]);
            Alert::error('Error', 'Tagihan gagal disimpan: ' . $e->getMessage());
        }

        return redirect()->route('buka-tagihan', ['bulan' => $request->bulan, 'tahun' => $request->tahun]);
    }

    /**
     * Tampilkan halaman buka tagihan dengan data langsung.
     */
    public function bukaTagihan(Request $request)
    {
        $bulanList = Bulan::all();
        // $tahunList = range(2021, date('Y') + 5);
        $currentYear = date('Y');

        $tahunList = range(
            $currentYear - 2,
            $currentYear + 2
        );

        // Default ke bulan dan tahun saat ini
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        // Ambil data tagihan (hanya yang belum lunas)
        $tagihanList = Tagihan::getDataByMonthYearAndStatus($bulan, $tahun, 'BL');

        return view('tagihan.buka-tagihan', compact('bulanList', 'tahunList', 'tagihanList', 'bulan', 'tahun'));
    }

    /**
     * Tampilkan data tagihan berdasarkan bulan dan tahun (redirect ke bukaTagihan).
     */
    public function dataTagihan(Request $request)
    {
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        return redirect()->route('buka-tagihan', ['bulan' => $bulan, 'tahun' => $tahun]);
    }

    /**
     * Proses pembayaran tagihan.
     * 
     * Menggunakan TagihanService untuk business logic pembayaran.
     */
    public function bayarTagihan(Request $request, $kode)
    {
        $tagihan = Tagihan::find($kode);

        if (!$tagihan) {
            Alert::error('Error', 'Tagihan tidak ditemukan');
            return redirect()->route('buka-tagihan');
        }

        // Gunakan service untuk proses pembayaran
        $jumlahBayar = $request->has('jumlah_bayar') ? (float) $request->input('jumlah_bayar') : null;
        $pembayaranVia = $request->input('pembayaran_via');
        $result = $this->tagihanService->processPayment($tagihan, $jumlahBayar, $pembayaranVia);

        if ($result['success']) {
            Alert::success('Sukses', $result['message']);
            return redirect()->route('buka-tagihan');
        }

        Alert::error('Error', $result['message']);
        return redirect()->route('buka-tagihan');
    }

    /**
     * Tampilkan halaman tagihan lunas.
     */
    public function lunasTagihan(Request $request)
    {
        $bulanList = Bulan::all();
        $tahunList = range(2021, date('Y') + 5);

        // Default ke bulan dan tahun saat ini
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        // Ambil data tagihan lunas
        $tagihanList = Tagihan::getDataByMonthYearAndStatus($bulan, $tahun, 'LS');

        return view('tagihan.lunas-tagihan', compact('bulanList', 'tahunList', 'tagihanList', 'bulan', 'tahun'));
    }

    /**
     * Cetak struk pembayaran.
     */
    public function cetakStruk($id)
    {
        $tagihan = Tagihan::find($id);

        if (!$tagihan) {
            return redirect()->route('buka-tagihan')->with('error', 'Tagihan tidak ditemukan');
        }

        $html = View::make('tagihan.cetak-struk', compact('tagihan'))->render();

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('struk_pembayaran.pdf');
    }

    /**
     * Tampilkan pelanggan yang sudah lunas.
     * 
     * Menggunakan TagihanService::getMonthNames() untuk nama bulan.
     */
    public function lunas(Request $request)
    {
        $selectedMonth = $request->query('bulan', Carbon::now()->month);
        $selectedYear = $request->query('tahun', Carbon::now()->year);
        $namaBulan = TagihanService::getMonthNames();

        $pelangganLunas = Pelanggan::where('status', 'aktif')
            ->whereHas('tagihan', function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('status', 'LS')
                    ->where('bulan', $selectedMonth)
                    ->where('tahun', $selectedYear);
            })->get();

        return view('tagihan.lunas', compact('pelangganLunas', 'selectedMonth', 'selectedYear', 'namaBulan'));
    }

    /**
     * Tampilkan pelanggan yang belum lunas.
     * 
     * Menggunakan TagihanService::getMonthNames() untuk nama bulan.
     */
    public function belumLunas(Request $request)
    {
        $selectedMonth = $request->query('bulan', Carbon::now()->month);
        $selectedYear = $request->query('tahun', Carbon::now()->year);
        $namaBulan = TagihanService::getMonthNames();

        $pelangganBelumLunas = Pelanggan::where('status', 'aktif')
            ->whereDoesntHave('tagihan', function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('status', 'LS')
                    ->where('bulan', $selectedMonth)
                    ->where('tahun', $selectedYear);
            })
            ->orWhere(function ($query) use ($selectedMonth, $selectedYear) {
                $query->whereHas('tagihan', function ($query) use ($selectedMonth, $selectedYear) {
                    $query->where('status', '!=', 'LS')
                        ->where('bulan', $selectedMonth)
                        ->where('tahun', $selectedYear);
                });
            })->get();

        return view('tagihan.belumLunas', compact('pelangganBelumLunas', 'selectedMonth', 'selectedYear', 'namaBulan'));
    }

    /**
     * Hapus tagihan.
     */
    public function deleteTagihan($id)
    {
        $tagihan = Tagihan::find($id);

        if (!$tagihan) {
            Alert::error('Error', 'Tagihan tidak ditemukan');
            return redirect()->route('buka-tagihan');
        }

        $tagihan->delete();

        Alert::success('Sukses', 'Tagihan berhasil dihapus');
        return redirect()->route('buka-tagihan');
    }

    /**
     * Generate tagihan bulanan otomatis.
     * 
     * Menggunakan TagihanService untuk business logic.
     */
    public function generateMonthlyBills()
    {
        $bulan = (int) Carbon::now()->format('m');
        $tahun = (int) Carbon::now()->format('Y');

        Log::info("🔄 Memulai pembuatan tagihan otomatis untuk bulan {$bulan} tahun {$tahun}");

        $result = $this->tagihanService->generateBillsForAllActiveCustomers($bulan, $tahun);

        return response()->json([
            'success' => true,
            'message' => 'Tagihan otomatis berhasil dibuat',
            'data' => $result
        ]);
    }

    /**
     * Rollback tagihan dari Lunas ke Belum Lunas.
     */
    public function rollbackTagihan($id)
    {
        try {
            $tagihan = Tagihan::findOrFail($id);
            
            // Reset status ke Belum Lunas
            $tagihan->status = 'BL';
            $tagihan->jumlah_dibayar = 0;
            $tagihan->save();

            Alert::success('Berhasil', 'Tagihan berhasil di-rollback ke Belum Lunas');
        } catch (\Exception $e) {
            Log::error('Error rollback tagihan', ['error' => $e->getMessage()]);
            Alert::error('Error', 'Gagal rollback tagihan: ' . $e->getMessage());
        }

        return redirect()->route('lunas-tagihan');
    }
}
