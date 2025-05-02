<?php

namespace App\Http\Controllers;

use App\Models\Bulan;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Paket;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Dompdf\Dompdf;
use PDF;


class TagihanController extends Controller
{
    public function index()
    {
        $bulanList = Bulan::all();
        $pelangganList = Pelanggan::where('status', 'aktif')->get();

        return view('tagihan.index', compact('bulanList', 'pelangganList'));
    }


    public function storeTagihan(Request $request)
    {
        // Validasi input dengan aturan yang lebih ketat
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12', // Pastikan bulan valid
            'tahun' => 'required|integer|min:2000', // Tahun minimum
            'id_pelanggan' => 'required|array|min:1', // Pastikan ada minimal 1 pelanggan
            'id_pelanggan.*' => 'exists:pelanggan,id_pelanggan', // Setiap ID pelanggan harus ada di tabel pelanggan
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $idPelangganBaru = []; // Array untuk menyimpan id_pelanggan baru

        try {
            // Logging input yang diterima
            Log::info('Data yang diterima untuk penyimpanan tagihan:', [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'id_pelanggan' => $request->id_pelanggan
            ]);

            // Iterasi setiap pelanggan dari array
            foreach ($request->id_pelanggan as $id_pelanggan) {
                // Cek apakah tagihan untuk pelanggan ini sudah ada di bulan dan tahun yang sama
                $existingTagihan = Tagihan::where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->where('id_pelanggan', $id_pelanggan)
                    ->first();

                if ($existingTagihan) {
                    Log::warning('Tagihan sudah ada untuk pelanggan ini. Melewati.', ['id_pelanggan' => $id_pelanggan]);
                    continue; // Skip jika tagihan sudah ada
                }

                // Tambahkan id_pelanggan baru ke array
                $idPelangganBaru[] = $id_pelanggan;
            }

            // Proses hanya pelanggan baru
            foreach ($idPelangganBaru as $id_pelanggan) {
                // Ambil pelanggan dengan relasi paket dan log jika pelanggan ditemukan
                $pelanggan = Pelanggan::with('paket')->findOrFail($id_pelanggan);
                Log::info('Pelanggan ditemukan:', ['id_pelanggan' => $id_pelanggan]);

                // Cek apakah pelanggan statusnya aktif
                if ($pelanggan->status == 'aktif') {
                    Log::info('Pelanggan aktif, ID:', ['id_pelanggan' => $id_pelanggan]);

                    // Pastikan relasi paket ada, jika tidak, log error dan lanjutkan ke pelanggan berikutnya
                    if ($pelanggan->paket) {
                        $tarifPelanggan = $pelanggan->paket->tarif;
                        Log::info('Tarif paket pelanggan:', ['id_pelanggan' => $id_pelanggan, 'tarif' => $tarifPelanggan]);

                        // Buat objek Tagihan baru
                        $tagihan = new Tagihan([
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                            'id_pelanggan' => $id_pelanggan,
                            'tagihan' => $tarifPelanggan,
                            'status' => 'BL', // Status 'BL' untuk tagihan baru
                        ]);

                        // Simpan tagihan ke database
                        $tagihan->save();
                        Log::info('Tagihan berhasil disimpan untuk pelanggan:', ['id_pelanggan' => $id_pelanggan]);

                    } else {
                        Log::warning('Pelanggan tidak memiliki paket, melewati.', ['id_pelanggan' => $id_pelanggan]);
                        continue; // Skip pelanggan yang tidak memiliki paket
                    }
                } else {
                    Log::warning('Pelanggan tidak aktif, melewati.', ['id_pelanggan' => $id_pelanggan]);
                }
            }

            // Jika semua tagihan berhasil disimpan, tampilkan alert sukses
            Alert::success('Sukses', 'Tagihan berhasil disimpan');
        } catch (\Exception $e) {
            // Tangkap error dan tampilkan alert error
            Alert::error('Error', 'Tagihan gagal disimpan. Pesan: ' . $e->getMessage());
        }

        // Redirect kembali ke halaman 'buka-tagihan'
        return redirect()->route('buka-tagihan');
    }

    public function bukaTagihan()
    {
        // Fetch the list of months and years
        $bulanList = Bulan::all();
        $tahunList = range(date('Y'), date('Y') + 5);

        $pelangganList = Pelanggan::where('status', 'aktif')->get();

        return view('tagihan.buka-tagihan', compact('bulanList', 'tahunList'));

    }

    public function dataTagihan(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Assuming you have a method to fetch data based on the month, year, and status
        $tagihanList = Tagihan::getDataByMonthYearAndStatus($bulan, $tahun, 'BL');

        return view('tagihan.data-tagihan', compact('tagihanList', 'bulan', 'tahun'));

    }

    public function bayarTagihan($kode)
    {
        // Temukan tagihan berdasarkan kode atau id_tagihan
        $tagihan = Tagihan::find($kode);

        // Cek apakah tagihan ditemukan
        if (!$tagihan) {
            Alert::error('Error', 'Tagihan tidak ditemukan');
            return redirect()->route('buka-tagihan');
        }

        // Update status dan tanggal bayar tanpa memeriksa apakah sudah lunas
        $tagihan->status = 'LS';
        $tagihan->tgl_bayar = now();
        $tagihan->save();

        Alert::success('Sukses', 'Pembayaran tagihan berhasil');
        return redirect()->route('lunas-tagihan');
    }


    public function lunasTagihan()
    {
        return view('tagihan.lunas-tagihan');
    }

    public function cetakStruk($id)
    {
        // Temukan tagihan berdasarkan ID
        $tagihan = Tagihan::find($id);

        // Pastikan tagihan ditemukan
        if (!$tagihan) {
            return redirect()->route('buka-tagihan')->with('error', 'Tagihan tidak ditemukan');
        }

        // Render view to HTML
        $html = View::make('tagihan.cetak-struk', compact('tagihan'))->render();

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
        return $dompdf->stream('struk_pembayaran.pdf');
    }

    public function lunas(Request $request)
    {
        // Ambil bulan dan tahun yang dipilih, jika tidak ada, gunakan bulan dan tahun saat ini
        $selectedMonth = $request->query('bulan', Carbon::now()->month);
        $selectedYear = $request->query('tahun', Carbon::now()->year);

        // Define the month names
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $pelangganLunas = Pelanggan::where('status', 'aktif')
            ->whereHas('tagihan', function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('status', 'LS')
                    ->where('bulan', $selectedMonth) // Filter berdasarkan bulan
                    ->where('tahun', $selectedYear); // Filter berdasarkan tahun
            })->get();

        return view('tagihan.lunas', compact('pelangganLunas', 'selectedMonth', 'selectedYear', 'namaBulan'));
    }

    public function belumLunas(Request $request)
    {
        // Ambil bulan dan tahun yang dipilih, jika tidak ada, gunakan bulan dan tahun saat ini
        $selectedMonth = $request->query('bulan', Carbon::now()->month);
        $selectedYear = $request->query('tahun', Carbon::now()->year);

        // Define the month names
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $pelangganBelumLunas = Pelanggan::where('status', 'aktif')
            ->whereDoesntHave('tagihan', function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('status', 'LS')
                    ->where('bulan', $selectedMonth) // Filter berdasarkan bulan
                    ->where('tahun', $selectedYear); // Filter berdasarkan tahun
            })
            ->orWhere(function ($query) use ($selectedMonth, $selectedYear) {
                $query->whereHas('tagihan', function ($query) use ($selectedMonth, $selectedYear) {
                    $query->where('status', '!=', 'LS')
                        ->where('bulan', $selectedMonth) // Filter berdasarkan bulan
                        ->where('tahun', $selectedYear); // Filter berdasarkan tahun
                });
            })->get();

        return view('tagihan.belumLunas', compact('pelangganBelumLunas', 'selectedMonth', 'selectedYear', 'namaBulan'));
    }


    public function deleteTagihan($id)
    {
        // Temukan tagihan berdasarkan ID
        $tagihan = Tagihan::find($id);

        // Cek apakah tagihan ditemukan
        if (!$tagihan) {
            Alert::error('Error', 'Tagihan tidak ditemukan');
            return redirect()->route('buka-tagihan');
        }

        // Hapus tagihan
        $tagihan->delete();

        Alert::success('Sukses', 'Tagihan berhasil dihapus');
        return redirect()->route('buka-tagihan');
    }

}





